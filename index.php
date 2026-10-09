<?php
/**
 * ফাইল নাম: index.php
 * কাজ: ওয়েবসাইটের মূল মাস্টার টেমপ্লেট এবং ডায়নামিক রাউটার (Router) 
 * যা সব কম্পোনেন্ট, উইজেট ও পেজ পাবলিক ভিউয়ের জন্য ডাইনামিকালি লোড করে।
 */

if (session_id() == '') {
    session_start();
}

// প্রয়োজনীয় কনফিগারেশন এবং ডাটাবেজ ফাইলগুলো যুক্ত করা
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/language.php';
require_once __DIR__ . '/includes/academic-data.php';

// পেজ রাউটিং হ্যান্ডেল করা
$page = isset($_GET['page']) ? trim($_GET['page']) : 'home';
$page = preg_replace('/[^a-zA-Z0-9\-_]/', '', $page);

$file_path = __DIR__ . "/pages/" . $page . ".php";
if (!file_exists($file_path)) {
    $file_path = __DIR__ . "/pages/404.php";
}
?>
<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'bn'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_setting('site_title', __('site_title')); ?></title>
    
    <!-- গ্লোবাল স্টাইল এবং রেসপনসিভ সিএসএস -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body style="margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: main-bg-color; color: #333;">

    <!-- ১. টপবার সেকশন (ভাষা পরিবর্তন ও কন্টাক্ট ইনফো) -->
    <div id="topbar-section">
        <?php include __DIR__ . '/includes/topbar.php'; ?>
    </div>

    <!-- ২. হেডার সেকশন (লোগো ও প্রতিষ্ঠানের নাম) -->
    <header style="background: main-bg-color; border-bottom: 0px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <?php include __DIR__ . '/includes/header.php'; ?>
    </header>
    <!-- ৪. রানিং নোটিশ / ব্রেকিং নিউজ টিককার -->
    <div id="running-notice-section">
        <?php include __DIR__ . '/includes/running-notice.php'; ?>
    </div>

    <!-- ৩. প্রধান নেভিগেশন মেনু (ডায়নামিক ড্রপডাউন সহ) -->
    <nav id="menu-section">
        <?php include __DIR__ . '/includes/menu.php'; ?>
    </nav>
    
    <!-- ৫. স্লাইডার সেকশন (শুধুমাত্র হোমপেজে দৃশ্যমান থাকবে) -->
    <?php if ($page === 'home'): ?>
        <section id="slider-section" style="width: 100%; margin-bottom: 0; line-height: 0;">
            <?php include __DIR__ . '/includes/slider.php'; ?>
        </section>

    <!--৬. স্লাইডারের ঠিক নিচে পুরো ওয়েব ভিউ বরাবর নোটিশবোর্ড সেকশন (গ্যাপ ছাড়া) -->
        <section id="home-notice-bar" style="margin-top: 0; padding-top: 0px; border-bottom: 0px solid #e0e0e0; box-sizing: border-box; width: 100%;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 4px 15px; background: #054538; border-radius: 0px; box-sizing: border-box;">
                <?php 
                // ডাটাবেজ থেকে সাম্প্রতিক 2 টি নোটিশ ফেচ করা
                $home_notices = [];
                try {
                    if (isset($pdo)) {
                        $stmt_n = $pdo->query("SELECT id, title, created_at, file FROM notices ORDER BY id DESC LIMIT 2");
                        $home_notices = $stmt_n->fetchAll(PDO::FETCH_ASSOC);
                    }
                } catch (Exception $e) {}
                ?>

 <div style="display: flex; justify-content: space-between; align-items: center;background: #006400; border: 2px solid black; padding: 4px; margin-bottom: 4px auto;">
 <h3 style="color: white; margin: 0px; font-size: 18px;">📌 নোটিশ বোর্ড (সকল)</h3>
 <a href="index.php?page=notices" style="font-size: 16px; color: white; text-decoration: none; font-weight: bold;">সকল নোটিশ দেখুন &raquo;</a>
 </div>

                <table border="0" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                    <?php if (!empty($home_notices)): ?>
                        <?php foreach ($home_notices as $not): ?>
                        <tr style="border: 2px solid rgba(255,255,255,0.2);">
                            <td style="width: 120px; font-size: 20px; color: white; white-space: nowrap;">
                                📅 <?php echo isset($not['created_at']) ? date('d-m-Y', strtotime($not['created_at'])) : 'N/A'; ?>
    </td>
    <td>
 <a href="index.php?page=notice-single&id=<?php echo $not['id']; ?>" style="text-decoration: none; color: white; font-size: 16px; font-weight: 600;">
            📎 <?php echo htmlspecialchars($not['title']); ?>
          </a>
    </td>
       <td style="width: 110px; text-align: right;">
          <?php if (!empty($not['file'])): ?>
  <a href="uploads/notices/<?php echo htmlspecialchars($not['file']); ?>" target="_blank" style="background: #008000; color: #fff; padding: 4px 8px; text-decoration: none; border-radius: 3px; font-size: 12px; display: inline-block;">📥 ডাউনলোড</a>
<?php else: ?>
  <a href="index.php?page=notice-single&id=<?php echo $not['id']; ?>" style="color: #006400; text-decoration: none; font-size: 16px; font-weight: bold;">বিস্তারিত &raquo;</a> 

  <?php endif; ?>
     </td>
     </tr>
     <?php endforeach; ?>
     <?php else: ?>
     <tr>
<td colspan="3" style="text-align: center; color: #ffffff; padding: 15px;">বর্তমানে কোনো নোটিশ প্রকাশিত হয়নি।</td>
     </tr>
     <?php endif; ?>
    </table>
   </div>
   </section>
    <?php endif; ?>

    <!-- ৭. মূল কন্টেন্ট এবং সাইডবার উইজেট এরিয়া (ফন্ট পেজ পাবলিক ভিউ গ্রিড) -->
    <div class="wrapper" style="max-width: 1200px; margin: 0px auto; display: flex; gap: 5px; flex-wrap: wrap; padding: 0 15px;">
        
        <!-- ডাইনামিক মেইন কন্টেন্ট পেজ -->
        <main style="flex: 3; min-width: 300px; background: #ffffff; padding: 25px; border: 1px solid #e0e0e0; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <?php 
            if ($page === 'home') {
                if (file_exists(__DIR__ . '/pages/home.php')) {
                    include __DIR__ . '/pages/home.php';
                } else {
                    echo '<div style="text-align: center; padding: 40px;">';
                    echo '<h2 style="color: #003366;">স্বাগতম বাণী</h2>';
                    echo '<p style="color: #666;">অনুগ্রহ করে আপনার অ্যাডমিন প্যানেল থেকে হোমপেজ কন্টেন্ট যুক্ত করুন।</p>';
                    echo '</div>';
                }
            } else {
                if (file_exists($file_path)) {
                    include $file_path;
                } else {
                    include __DIR__ . '/pages/404.php';
                }
            }
            ?>
        </main>

        <!-- ডাইনামিক সাইডবার (নোটিশবোর্ড, শিক্ষক প্যানেল ও গুরুত্বপূর্ণ লিংক) -->
        <aside style="flex: 1; min-width: 260px;">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </aside>

    </div>

    <!-- ৮. ফুটার সেকশন (পরিচিতি, ঠিকানা, গুগল ম্যাপ ও কপিরাইট) -->
    <footer style="background: #1a1a1a; color: #ffffff; margin-top: 0px; border-top: 4px solid #003366;">
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </footer>

    <!-- মূল জাভাস্ক্রিপ্ট ফাইল -->
    <script src="/my-dynamic-website/assets/js/main.js"></script>
</body>
</html>