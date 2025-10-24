<?php
session_start();
require_once '../connection.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$case_id = (int)$_GET['id'];
$sql = "SELECT * FROM cases WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $case_id);
$stmt->execute();
$result = $stmt->get_result();
$case = $result->fetch_assoc();

if (!$case) {
    header('Location: index.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $purok = mysqli_real_escape_string($conn, $_POST['purok']);
    $barangay = mysqli_real_escape_string($conn, $_POST['barangay']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $date = date('Y-m-d', strtotime($_POST['date']));
    $time = date('H:i:s', strtotime($_POST['time']));
    $police_notified = mysqli_real_escape_string($conn, $_POST['police_notified']);
    $incident_type = mysqli_real_escape_string($conn, $_POST['incident_type']);
    $injuries_sustained = mysqli_real_escape_string($conn, $_POST['injuries_sustained']);

    // Update the database
    $update_sql = "UPDATE cases SET name=?, purok=?, barangay=?, location=?, incident_date=?, 
                   incident_time=?, police_notified=?, incident_type=?, injuries_sustained=? 
                   WHERE id=?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssssi", $name, $purok, $barangay, $location, $date, $time, 
                           $police_notified, $incident_type, $injuries_sustained, $case_id);
    
    if ($update_stmt->execute()) {
        try {
            // Delete old files if they exist
            if ($case['doc_path'] && file_exists(__DIR__ . '/generated/' . $case['doc_path'])) {
                unlink(__DIR__ . '/generated/' . $case['doc_path']);
            }
            if ($case['pdf_path'] && file_exists(__DIR__ . '/generated/' . $case['pdf_path'])) {
                unlink(__DIR__ . '/generated/' . $case['pdf_path']);
            }

            // Create directory if it doesn't exist
            if (!is_dir(__DIR__ . '/generated')) {
                mkdir(__DIR__ . '/generated', 0755, true);
            }

            // Load and process template
            $templateProcessor = new TemplateProcessor(__DIR__ . '/cases.docx');
            
            // Format the name for filename (replace spaces with underscores and remove special characters)
            $formatted_name = preg_replace('/[^a-zA-Z0-9]/', '_', $name);
            $formatted_name = strtolower(trim($formatted_name, '_'));
            
            // Replace variables in the template
            $templateProcessor->setValue('${NAME}', strtoupper($name));
            $templateProcessor->setValue('name', $name);
            $templateProcessor->setValue('purok', $purok);
            $templateProcessor->setValue('barangay', $barangay);
            $templateProcessor->setValue('location', $location);
            $templateProcessor->setValue('date', date('F j, Y', strtotime($date)));
            $templateProcessor->setValue('time', date('g:i A', strtotime($time)));
            $templateProcessor->setValue('yesOrNo', $police_notified);
            $templateProcessor->setValue('incidentType', $incident_type);
            $templateProcessor->setValue('injuriesSustained', $injuries_sustained);
            
            // Save the generated document with formatted name
            $doc_filename = $formatted_name . '_case_' . $case_id . '_' . date('Ymd_His') . '.docx';
            $doc_path = __DIR__ . '/generated/' . $doc_filename;
            $templateProcessor->saveAs($doc_path);
            
            if (!file_exists($doc_path)) {
                throw new Exception('Generated Word document not found');
            }
            
            // Convert to PDF using Cloudmersive API
            $api_url = 'https://api.cloudmersive.com/convert/docx/to/pdf';
            $api_key = 'd1405d35-69c7-47a6-b590-013a31407deb';

            $file = new CURLFile(
                $doc_path,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                basename($doc_path)
            );
            
            $post_data = [
                'inputFile' => $file
            ];

            $pdf_filename = $formatted_name . '_case_' . $case_id . '_' . date('Ymd_His') . '.pdf';
            $pdf_path = __DIR__ . '/generated/' . $pdf_filename;

            $ch = curl_init();
            $verbose = fopen('php://temp', 'w+');
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_VERBOSE => true,
                CURLOPT_STDERR => $verbose,
                CURLOPT_POSTFIELDS => $post_data,
                CURLOPT_HTTPHEADER => [
                    'Apikey: ' . $api_key,
                    'Content-Type: multipart/form-data'
                ]
            ]);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception('cURL Error: ' . curl_error($ch));
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($http_code !== 200) {
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                fclose($verbose);

                $responseData = json_decode($response, true);
                $errorMessage = isset($responseData['message']) ? $responseData['message'] : $response;

                throw new Exception(sprintf(
                    "API Error: HTTP Status %d\nResponse: %s\nContent-Type: %s\nCURL Error: %s\nDebug Log: %s",
                    $http_code,
                    substr($errorMessage, 0, 1000),
                    $content_type,
                    $error,
                    $verboseLog
                ));
            }
            
            if (!$response) {
                throw new Exception('No response received from API');
            }
            
            if (file_put_contents($pdf_path, $response) !== false) {
                // Update database with new file paths
                $update_sql = "UPDATE cases SET doc_path = ?, pdf_path = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ssi", $doc_filename, $pdf_filename, $case_id);
                
                if ($update_stmt->execute()) {
                    header("Location: view_case.php?id=" . $case_id);
                    exit();
                } else {
                    throw new Exception('Failed to update database with file paths');
                }
            } else {
                throw new Exception('Failed to save PDF file');
            }
        } catch (Exception $e) {
            $errors[] = "Error processing document: " . $e->getMessage();
        }
    } else {
        $errors[] = "Error updating case in database";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Case Report #<?php echo $case_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .card { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); transition: all 0.3s ease; }
        .card:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h1 class="text-xl font-bold tracking-tight">Edit Case Report</h1>
                </div>
                <nav class="flex items-center space-x-4">
                    <a class="hover:text-blue-100 transition-colors duration-200 font-medium" href="../pamanlinan.php">Dashboard</a>
                    <a class="hover:text-blue-100 transition-colors duration-200 font-medium" href="../bulletinBoard.php">Bulletin Board</a>
                    <a class="hover:text-blue-100 transition-colors duration-200 font-medium" href="index.php">Cases</a>
                    <a class="hover:text-blue-100 transition-colors duration-200 font-medium" href="../logout.php">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <section class="mb-8">
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-gray-900">Edit Case Report #<?php echo $case_id; ?></h2>
                <div>
                    <a href="view_case.php?id=<?php echo $case_id; ?>" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Case Details
                    </a>
                </div>
            </div>
        </section>

        <?php if($errors): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6">
                <?php foreach($errors as $e) echo '<div class="mb-1">'.htmlspecialchars($e).'</div>'; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-xl card">
            <form method="post" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input 
                            name="name" 
                            required
                            value="<?php echo htmlspecialchars($case['name']); ?>"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Incident Type</label>
                        <input 
                            name="incident_type" 
                            required
                            value="<?php echo htmlspecialchars($case['incident_type']); ?>"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purok</label>
                        <input 
                            name="purok" 
                            required
                            value="<?php echo htmlspecialchars($case['purok']); ?>"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                        <input 
                            name="barangay" 
                            value="<?php echo htmlspecialchars($case['barangay']); ?>"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                        />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <textarea 
                            name="location" 
                            required
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3"
                        ><?php echo htmlspecialchars($case['location']); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input 
                            type="date"
                            name="date" 
                            required
                            value="<?php echo date('Y-m-d', strtotime($case['incident_date'])); ?>"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                        <input 
                            type="time"
                            name="time" 
                            required
                            value="<?php echo date('H:i', strtotime($case['incident_time'])); ?>"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Police Notified</label>
                        <select 
                            name="police_notified" 
                            required
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3"
                        >
                            <option value="Yes" <?php echo $case['police_notified'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No" <?php echo $case['police_notified'] === 'No' ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Injuries Sustained</label>
                        <textarea 
                            name="injuries_sustained" 
                            rows="3"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3"
                        ><?php echo htmlspecialchars($case['injuries_sustained']); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6">
                    <a 
                        href="view_case.php?id=<?php echo $case_id; ?>" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-500">
                © <?php echo date('Y'); ?> Barangay Pamanlinan. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Date validation for Philippines timezone
        var dateInput = document.querySelector('input[type="date"]');
        
        // Get current date in Philippines timezone (UTC+8)
        var options = { timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit' };
        var today = new Date().toLocaleString('en-US', options).split('/');
        var currentDate = today[2] + '-' + today[0].padStart(2, '0') + '-' + today[1].padStart(2, '0');
        
        dateInput.max = currentDate;
        
        // Validate on input
        dateInput.addEventListener('input', function() {
            if(this.value > currentDate) {
                this.value = currentDate;
            }
        });
    </script>
</body>
</html>