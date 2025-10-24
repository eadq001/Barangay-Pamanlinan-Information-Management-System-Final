<?php
require_once '../connection.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Case Report #<?php echo $case_id; ?></title>
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
                    <h1 class="text-xl font-bold tracking-tight">Case Report Details</h1>
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
                <h2 class="text-3xl font-bold text-gray-900">Case Report #<?php echo $case_id; ?></h2>
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

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-xl card">
                    <div class="mb-6 flex justify-end space-x-4">
                        <?php if ($case['pdf_path']): ?>
                        <a href="generated/<?php echo htmlspecialchars($case['pdf_path']); ?>" 
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            View PDF
                        </a>
                        <?php endif; ?>
                        <a href="edit_case.php?id=<?php echo $case_id; ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Case
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3"><?php echo htmlspecialchars($case['name']); ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Incident Type</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3"><?php echo htmlspecialchars($case['incident_type']); ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purok</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3"><?php echo htmlspecialchars($case['purok']); ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3"><?php echo htmlspecialchars($case['barangay']); ?></p>
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3"><?php echo htmlspecialchars($case['location']); ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3">
                                <?php echo date('F j, Y', strtotime($case['incident_date'])); ?>
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3">
                                <?php echo date('g:i A', strtotime($case['incident_time'])); ?>
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Police Notified</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $case['police_notified'] === 'Yes' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo htmlspecialchars($case['police_notified']); ?>
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Injuries Sustained</label>
                            <div class="text-gray-900 bg-gray-50 rounded-lg p-3 prose prose-sm max-w-none">
                                <?php echo nl2br(htmlspecialchars($case['injuries_sustained'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl card p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Case Information</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center space-x-2">
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Created on: <?php echo date('F j, Y', strtotime($case['created_at'])); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Time: <?php echo date('g:i A', strtotime($case['created_at'])); ?></span>
                        </li>
                        <?php if ($case['pdf_path']): ?>
                        <li class="flex items-center space-x-2">
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>PDF Report Available</span>
                        </li>
                        <?php endif; ?>
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
</body>
</html>