<?php
session_start();
require_once '../connection.php';

$isLoggedIn = isset($_SESSION['user_id']);

$query = "SELECT * FROM cases ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cases & Incident Reports</title>
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
          <h1 class="text-xl font-bold tracking-tight">Cases & Incident Reports</h1>
        </div>
        <nav class="flex items-center space-x-4">
          <a class="hover:text-blue-100 transition-colors duration-200 font-medium" href="../pamanlinan.php">Dashboard</a>
          <a class="hover:text-blue-100 transition-colors duration-200 font-medium" href="../bulletinBoard.php">Bulletins</a>
          <?php if($isLoggedIn): ?>
            <a class="bg-white text-blue-700 px-4 py-2 rounded-lg shadow-sm hover:bg-blue-50 transition-colors duration-200 font-medium" href="cases.php">Create New Case</a>
            <a class="hover:text-blue-100 transition-colors duration-200 font-medium" href="../logout.php">Logout</a>
          <?php else: ?>
            <a class="bg-white text-blue-700 px-4 py-2 rounded-lg shadow-sm hover:bg-blue-50 transition-colors duration-200 font-medium" href="../login.php">Login</a>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <section class="mb-8">
      <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Recent Cases & Reports</h2>
        <div>
          <a href="index.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 transition-colors duration-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </a>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <?php if(mysqli_num_rows($result) == 0): ?>
          <div class="bg-white p-8 rounded-lg card text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-lg">No cases found.</p>
          </div>
        <?php else: ?>
          <?php while($case = mysqli_fetch_assoc($result)): ?>
            <article class="bg-white rounded-xl card mb-6 overflow-hidden">
              <div class="p-6">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                      Case #<?php echo $case['id']; ?> - <?php echo htmlspecialchars($case['name']); ?>
                    </h3>
                    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <?php echo htmlspecialchars($case['incident_type']); ?>
                      </span>
                      <span>•</span>
                      <time datetime="<?php echo $case['incident_date']; ?>">
                        <?php echo date('F j, Y', strtotime($case['incident_date'])) . ' at ' . 
                             date('g:i A', strtotime($case['incident_time'])); ?>
                      </time>
                    </div>
                  </div>
                </div>

                <div class="prose prose-blue max-w-none text-gray-600">
                  <p><strong>Location:</strong> <?php echo htmlspecialchars($case['location']); ?></p>
                  <p><strong>Purok:</strong> <?php echo htmlspecialchars($case['purok']); ?></p>
                  <p><strong>Police Notified:</strong> <?php echo htmlspecialchars($case['police_notified']); ?></p>
                  <?php if($case['injuries_sustained']): ?>
                    <p><strong>Injuries:</strong> <?php echo nl2br(htmlspecialchars(substr($case['injuries_sustained'], 0, 200))); ?>
                    <?php if(strlen($case['injuries_sustained']) > 200): ?>
                      <span class="text-blue-600">...</span>
                    <?php endif; ?></p>
                  <?php endif; ?>
                </div>

                <div class="mt-6 flex items-center space-x-4 text-sm">
                  <a href="view_case.php?id=<?php echo $case['id']; ?>" 
                     class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors duration-200">
                    <span>View Details</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                  </a>
                  <?php if($case['pdf_path']): ?>
                    <a href="generated/<?php echo htmlspecialchars($case['pdf_path']); ?>" 
                       target="_blank"
                       class="text-red-600 hover:text-red-700 transition-colors duration-200">
                      View PDF
                    </a>
                  <?php endif; ?>
                  <?php if($isLoggedIn): ?>
                    <?php if($case['doc_path']): ?>
                      <a href="generated/<?php echo htmlspecialchars($case['doc_path']); ?>" 
                         class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        Download DOCX
                      </a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

      <aside class="space-y-6">
        <!-- Quick Stats Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl card p-6">
          <h4 class="text-lg font-bold text-gray-900 mb-4">Case Statistics</h4>
          <?php
            $stats_q = mysqli_query($conn, "SELECT 
              COUNT(*) as total,
              COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today,
              COUNT(CASE WHEN police_notified = 'Yes' THEN 1 END) as police_notified
              FROM cases");
            $stats = mysqli_fetch_assoc($stats_q);
          ?>
          <div class="grid grid-cols-3 gap-4 text-center">
            <div class="p-3">
              <div class="text-2xl font-bold text-blue-600"><?php echo $stats['total']; ?></div>
              <div class="text-xs text-gray-600">Total Cases</div>
            </div>
            <div class="p-3">
              <div class="text-2xl font-bold text-blue-600"><?php echo $stats['today']; ?></div>
              <div class="text-xs text-gray-600">Today's Cases</div>
            </div>
            <div class="p-3">
              <div class="text-2xl font-bold text-blue-600"><?php echo $stats['police_notified']; ?></div>
              <div class="text-xs text-gray-600">Police Notified</div>
            </div>
          </div>
        </div>

        <!-- Recent Cases by Type -->
        <div class="bg-white rounded-xl card p-6">
          <div class="flex items-center justify-between mb-6">
            <h4 class="text-lg font-bold text-gray-900">Cases by Type</h4>
          </div>
          <?php
            $types_q = mysqli_query($conn, "SELECT incident_type, COUNT(*) as count 
                                          FROM cases 
                                          GROUP BY incident_type 
                                          ORDER BY count DESC 
                                          LIMIT 5");
          ?>
          <div class="space-y-4">
            <?php while($type = mysqli_fetch_assoc($types_q)): ?>
              <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($type['incident_type']); ?></span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  <?php echo $type['count']; ?> cases
                </span>
              </div>
            <?php endwhile; ?>
          </div>
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