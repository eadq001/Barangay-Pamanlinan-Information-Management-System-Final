<?php
session_start();
require_once '../connection.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
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

    // Insert into database
    $sql = "INSERT INTO cases (name, purok, barangay, location, incident_date, incident_time, 
            police_notified, incident_type, injuries_sustained) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $name, $purok, $barangay, $location, $date, $time, 
                      $police_notified, $incident_type, $injuries_sustained);
    
    if ($stmt->execute()) {
        $case_id = $stmt->insert_id;
        
        try {
            // Create directory if it doesn't exist
            if (!is_dir(__DIR__ . '/generated')) {
                mkdir(__DIR__ . '/generated', 0755, true);
            }

            // Load and process template
            $templateProcessor = new TemplateProcessor(__DIR__ . '/cases.docx');
            
            // Replace variables - using both ${NAME} and name formats for compatibility
            $templateProcessor->setValue('${NAME}', strtoupper($name));
            $templateProcessor->setValue('name', $name);

            $templateProcessor->setValue('name', $name); // keeping original as backup
            $templateProcessor->setValue('purok', $purok);
            $templateProcessor->setValue('barangay', $barangay);
            $templateProcessor->setValue('location', $location);
            $templateProcessor->setValue('date', date('F j, Y', strtotime($date)));
            $templateProcessor->setValue('time', date('g:i A', strtotime($time)));
            $templateProcessor->setValue('yesOrNo', $police_notified);
            $templateProcessor->setValue('incidentType', $incident_type);
            $templateProcessor->setValue('injuriesSustained', $injuries_sustained);
            
            // Save the generated document
            $doc_filename = 'case_' . $case_id . '_' . date('Ymd_His') . '.docx';
            $doc_path = __DIR__ . '/generated/' . $doc_filename;
            $templateProcessor->saveAs($doc_path);
            
            // Ensure the Word document exists before proceeding
            if (!file_exists($doc_path)) {
                throw new Exception('Generated Word document not found');
            }
            
            // Convert to PDF using Cloudmersive API
            $api_url = 'https://api.cloudmersive.com/convert/docx/to/pdf';
            $api_key = 'd1405d35-69c7-47a6-b590-013a31407deb';

            // Initialize cURL with proper file type and encoding
            $file = new CURLFile(
                $doc_path,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                basename($doc_path)
            );
            
            // Prepare the request data with required parameters
            $post_data = [
                'inputFile' => $file
            ];

            // Prepare PDF filename/path early so a fallback conversion can write to it
            $pdf_filename = 'case_' . $case_id . '_' . date('Ymd_His') . '.pdf';
            $pdf_path = __DIR__ . '/generated/' . $pdf_filename;

            // Setup cURL request with verbose debug output
            $ch = curl_init();
            
            // Create a temporary file for CURL debug output
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
            
            // Execute request and check response
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception('cURL Error: ' . curl_error($ch));
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($http_code !== 200) {
                // Get the CURL debug output
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);

                // Try to decode response as JSON for more details
                $responseData = json_decode($response, true);
                $errorMessage = isset($responseData['message']) ? $responseData['message'] : $response;

                // If the API responds with 404, attempt a local conversion fallback (LibreOffice/soffice)
                if ($http_code === 404) {
                    $converted = false;

                    // Candidate executable names/paths for headless conversion
                    $candidates = [
                        'soffice',
                        'libreoffice',
                        'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                        'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe'
                    ];

                    foreach ($candidates as $candidate) {
                        $checkCmd = escapeshellcmd($candidate) . ' --version 2>&1';
                        @exec($checkCmd, $out, $ret);
                        if (isset($ret) && $ret === 0) {
                            $soffice = $candidate;
                            break;
                        }
                    }

                    if (isset($soffice)) {
                        $convertCmd = escapeshellcmd($soffice) . ' --headless --convert-to pdf --outdir ' . escapeshellarg(__DIR__ . '/generated') . ' ' . escapeshellarg($doc_path) . ' 2>&1';
                        @exec($convertCmd, $out, $ret);
                        // soffice will create <basename>.pdf in the outdir
                        $candidatePdf = __DIR__ . '/generated/' . pathinfo($doc_path, PATHINFO_FILENAME) . '.pdf';
                        if (($ret ?? 1) === 0 && file_exists($candidatePdf)) {
                            // Rename to the expected target filename if different
                            if ($candidatePdf !== $pdf_path) {
                                @rename($candidatePdf, $pdf_path);
                            }
                            $converted = true;
                        }
                    }

                    if ($converted) {
                        // Close the verbose debug file
                        fclose($verbose);

                        // Update database with file paths and redirect (same flow as successful API conversion)
                        $update_sql = "UPDATE cases SET doc_path = ?, pdf_path = ? WHERE id = ?";
                        $update_stmt = $conn->prepare($update_sql);
                        $update_stmt->bind_param("ssi", $doc_filename, $pdf_filename, $case_id);

                        if ($update_stmt->execute()) {
                            header("Location: view_case.php?id=" . $case_id);
                            exit();
                        } else {
                            throw new Exception('Failed to update database with file paths after local conversion');
                        }
                    }

                    // If we reach here, local conversion failed — include verbose log to help debug
                    throw new Exception(sprintf(
                        "API Error: HTTP Status %d\nResponse: %s\nContent-Type: %s\nCURL Error: %s\nDebug Log: %s\nNote: attempted local conversion but it failed or LibreOffice is not installed.",
                        $http_code,
                        substr($errorMessage, 0, 1000),
                        $content_type,
                        $error,
                        $verboseLog
                    ));
                }

                // Non-404 API errors — include verbose log for debugging
                fclose($verbose);
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
            
            // Save the PDF and update database
            $pdf_filename = 'case_' . $case_id . '_' . date('Ymd_His') . '.pdf';
            $pdf_path = __DIR__ . '/generated/' . $pdf_filename;
            
            if (file_put_contents($pdf_path, $response) !== false) {
                // Update database with file paths
                $update_sql = "UPDATE cases SET doc_path = ?, pdf_path = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ssi", $doc_filename, $pdf_filename, $case_id);
                
                if ($update_stmt->execute()) {
                    header("Location: view_case.php?id=" . $case_id);
                    exit();
                } else {
                                $log_filename = 'compdf_debug_' . date('Ymd_His') . '.log';
                                $log_path = __DIR__ . '/generated/' . $log_filename;
                                @file_put_contents($log_path, $verboseLog);
                    throw new Exception('Failed to update database with file paths');
                }
            } else {
                throw new Exception('Failed to save PDF file');
            }
        } catch (Exception $e) {
            $errors[] = "Error processing document: " . $e->getMessage();
        }
    } else {
        $errors[] = "Error saving case to database";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Case Report</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h1 class="text-xl font-bold tracking-tight">Create New Case Report</h1>
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
                <h2 class="text-3xl font-bold text-gray-900">Create Case Report</h2>
                <div>
                    <a href="index.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Cases
                    </a>
                </div>
            </div>
        </section>

        <?php if($errors): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6">
                <?php foreach($errors as $e) echo '<div class="mb-1">'.htmlspecialchars($e).'</div>'; ?>
            </div>
        <?php endif; ?>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <form method="post" class="bg-white p-8 rounded-xl card">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input 
                                name="name" 
                                required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                                placeholder="Enter full name"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Purok</label>
                                <input 
                                    name="purok" 
                                    required
                                    class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                                    placeholder="Enter purok"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                                <input 
                                    name="barangay" 
                                    class="w-full border border-gray-300 rounded-lg shadow-sm bg-gray-50 p-3" 
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <textarea 
                                name="location" 
                                required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3"
                                placeholder="Enter specific location of incident"
                                rows="2"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input 
                                    name="date" 
                                    type="date"
                                    required 
                                    class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                                <input 
                                    name="time" 
                                    type="time"
                                    required
                                    class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Police Notified</label>
                            <select 
                                name="police_notified" 
                                required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3"
                            >
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type of Incident</label>
                            <input 
                                name="incident_type" 
                                required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3" 
                                placeholder="Enter type of incident"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Injuries Sustained</label>
                            <textarea 
                                name="injuries_sustained" 
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3"
                                placeholder="Describe any injuries sustained (if applicable)"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a 
                                href="index.php" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Cancel
                            </a>
                            <button 
                                type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Create Case Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <aside class="space-y-6">
                <!-- Case Guidelines Card -->
                <div class="bg-white rounded-xl card p-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h4 class="text-lg font-bold text-gray-900">Case Filing Guidelines</h4>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Provide accurate personal information</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Include specific location details</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Report exact date and time</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Describe any injuries in detail</span>
                        </li>
                    </ul>
                </div>

                <!-- Quick Tips Card -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl card p-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <h4 class="text-lg font-bold text-gray-900">Quick Tips</h4>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span>Be precise with incident details</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span>Include all relevant information</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span>Review before submitting</span>
                        </li>
                    </ul>
                </div>
            </aside>
        </section>
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

        console.log('Current date (Philippines):', currentDate);
    </script>
</body>
</html>