<?php
//define('SITEURL', 'http://sangramold');
define('SITEURL', 'http://www.dailysangram.com');
define('SITETITLE', 'দৈনিক সংগ্রাম');
//define('SITEURL', 'http://192.185.226.192/~dailysan');
define('MYDATABASE', 'dailysan_newsangram');
define('THUMBS_IMAGE', './images/thumbs/');
define('MYTIMEZONE', 'Asia/Dhaka');
define('SHORT_URL_API_KEY', 'AIzaSyDDIR9DMIoXkqwc1UQuQlD-ooynxuBbTGA');
date_default_timezone_set(MYTIMEZONE);
//if (!ini_get('display_errors')) {
//    ini_set('display_errors', '1');
//}
include_once('./system/system_config.php');
include_once('./system/library/cache_manager/Cache_manager.php'); // include php fast cache
include_once('./system/library/simple_php_cache/cache.class.php'); // include simple php cache
include_once('./system/library/paginator/paginator.php'); // include paginator

// Mobile Detect
$detect = new Mobile_Detect();
$is_mobile = ($detect->isMobile() || $detect->isTablet()) ? true : false;

// cache
$cache = new Cache_manager();

$message = false;
$meta_keywords = "Bangladesh News, business news, international news, google news, yahoo news, cnn, bbc, aljazeera, world news, foreign mission, bangladesh mission, icc, cricket, world cup, 2011,  Free Advertisement, free Ad, free Ad on the net, buy-sell, buy &amp; sell, buy and sell, Advertisement on the Net, Horoscope, horoscope, IT, ICT, Business, Health, health, Media, TV, Radio, Dhaka News, World News, National News, Bangladesh Media, Betar, Current News, Weather, foreign exchange rate, Foreign Exchange Rate, Education, Foreign Education, Higher Education, Family, Relationship, Sports, sports, Bangladesh Sports, Bangladesh, Bangladesh Politics, Bangladesh Business, Economical news, Update news, International News, Islamic World, Islam and Muslims, Bangladesh History and Heritage, Bangladeshi Muslims, Politics, Bangladeshi Politics, Islam and Media, Human Rights, Sports News, Women, Health, Agriculture, Information Technology news, Education, Literature, dailysangram.com, dailysangram.net দৈনিক সংগ্রাম, বাংলাদেশ, বাংলাদেশী সংবাদ, বাংলাদেশী নিউজ, ইসলাম, ইসলামী নিউজ, অর্থনীতি সংবাদ, রাজনীতি, মানবাধিকার, আন্তর্জাতিক সংবাদ, নারী সংবাদ, কৃষি সংবাদ, খেলার সংবাদ, বাংলাদেশ ও ইসলাম, ইসলাম ও মুসলমান, স্বাধীনতা, স্বাধীনতা ও ইসলাম, ইতিহাস ঐতিহ্য, আইটি নিউজ, চাকরীর সংবাদ, শিক্ষা সাহিত্য, The Daily Sangram, Daily Sangram, Newspaper, Bangladeshi newspaper, Bangladeshi news, Islam, Islamic news, Dhaka, Bangla, News, news, Bangladesh, Bangladeshi, Bengali, Culture, Portal Site, Dhaka, textile, garments, micro credit, Daily Sangram, Sangram, www.dailysangram.com, www.dailysangram.net, sangram.net, dailysangram.net, sangram.com, dailysangram.com, Bangladesh Newspapers, Bangladesh News, News From Bangladesh, Bangladesh Sports News, Bangladesh Financial News, World News, National News, Bangladesh Media, Betar, Current News, Weather, weather, foreign exchange rate, Foreign Exchange Rate, Education, Foreign Education,Higher Education, Family, Relationship, Sports, sports, Bangladesh Sports, Bangladesh, Bangladesh Politics, Bangladesh Business";
$meta_description = "The Daily Sangram is an oldest bangla daily newspaper. It publishes trusted Bangladesh and International news that includes business IT, ICT, health, weather, foreign affairs, education, family, sports, politics, economics, Information about holidays, vacations, resorts, real estate and property together with finance, stock market and investments reports, theater, movies, culture, World News, National News, Bangladesh Media, Betar, Current News, Weather, weather, foreign exchange rate, Foreign Exchange Rate, Education, Foreign Education,Higher Education, Family, Relationship, Sports, sports, Bangladesh Sports, Bangladesh, Bangladesh Politics, Bangladesh Business, entertainment, Islamic world History and Heritage and women rights news. ";

$title = 'The Daily Sangram';

$removable_characters = array('!', '~', '`', '@', '#', '$', '%', '^', '&', '*', '_', '+', '=', '(', ')', '{', '}', '[', ']', '|', '"', "'", ':', 
                        ';', '\\', '/', '?', '>', '<', '.', ',', '‘', '’', '॥ ', ' ॥');
// redirect when 'sub menu' form is run on mobile theme
if(isset($_POST['sub_section'])) header("Location: ". SITEURL . $_POST['sub_section']);

// if(!array_key_exists('post', $_GET)) $connection = dbConnect('read', MYDATABASE);

$cache_dir_rss = './' . $cache_directory . 'rss/';
$cache_key_rss = 'rss_print';
$cache_dir_section = './' . $cache_directory . 'section/';
$cache_key_section = 'sections';
$cache_dir_section_page = './' . $cache_directory . 'section_page/';
$cache_key_section_page = false;
$cache_dir_category = './' . $cache_directory . 'category/';
$cache_key_category = 'categories';
$cache_dir_issue = './' . $cache_directory . 'issue/';
$cache_key_issue = 'iss_';
$cache_dir_post = './' . $cache_directory . 'posts/';
$cache_key_post = '';
$cache_dir_home = './' . $cache_directory . 'home/';
$cache_key_home = '';
$cache_dir_latest_news = './' . $cache_directory . 'latest_news/';
$cache_key_latest_news = 'latest_news';
$cache_dir_daily_page = './' . $cache_directory . 'daily_pages/';
$cache_key_daily_page = false;
$cache_dir_page = './' . $cache_directory . 'page/';
$cache_key_page = false;
$issue_date = null;

// connection with pdo
$user = 'dailysan_dsquery';
$pwd = '(8aM<|Q0&eB!++,dBn*';
// if(!array_key_exists('post', $_GET)) $conn = db_connect($user, $pwd, MYDATABASE);

$post_uri = array('id' => false);
$page = array('id' => false, 'alias' => false, 'title' => false);
$pages = array();
$page_no = 0;
$pagination_uri = '';
$publication_date = array('id' => false, 'date' => false, 'description' => false);
$section = array('id' => false, 'alias' => false, 'title' => false);
$category = array('id' => false, 'alias' => false, 'title' => false);
$sections = array();
$categories = array();
$date_line = array('date' => false, 'description' => false);
$main_menu = '';
$main_sub_menu = '';
$breadcrumbs = '';

// archve search
if(array_key_exists('archvesearch', $_POST)){
    if(checkdate($_POST['archivemonth'], $_POST['archiveday'], $_POST['archiveyear'])){
        $archive_date = $_POST['archiveyear'] . '-' . $_POST['archivemonth'] . '-' . $_POST['archiveday'];
        header("Location: ".SITEURL."/page/first-page/$archive_date");
    }
}

// query for section
//$sections = get_cache($cache_key_section, $cache_dir_section); // sections for cache
$sections = $cache->get_item($cache_key_section); //sections for cache
if(is_null($sections)) {
    $sql = "SELECT id, title, alias FROM section ORDER BY 'order' ASC";
    $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
    $result = $connection->query($sql) or die(mysqli_error($connection));
    while ($rows = $result->fetch_assoc()) {
        $sections[$rows['id']] = array('title' => $rows['title'], 'alias' => $rows['alias']);
    }
    $cache->set_item($sections); // create section cache;
} // ***************************** end section *****************************************


// query for category
//$categories = get_cache($cache_key_category, $cache_dir_category); // category for cache
$categories = $cache->get_item($cache_key_category);
if(!$categories){
    $sql = "SELECT id, title, alias, section FROM category ORDER BY title ASC";
    $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
    $result = $connection->query($sql) or die(mysqli_error($connection));
    while ($rows = $result->fetch_assoc()) {
        $categories[$rows['section']][] = array('id' => $rows['id'], 'title' => $rows['title'], 'alias' => $rows['alias']);
    }
    $cache->set_item($categories); // create category cache;
}


// make menu from section & category
if(array_key_exists('section', $_GET)){ // get section name
    $alias = explode('/', $_GET['section']);
    $section['alias'] = $alias[0];
//    var_dump($alias[1]);
    $category['alias'] = (isset($alias[1]) && !is_numeric($alias[1])) ? $alias[1] : false;
    $page_no = isset($alias[2]) ? floor($alias[2]) : (isset($alias[1]) && is_numeric($alias[1]) ? floor($alias[1]) : $page_no);
    $pagination_uri = $category['alias'] ? "/section/" . $section['alias'] . "/" . $category['alias'] :  "/section/" . $section['alias'];
}

// post details
if(array_key_exists('post', $_GET)){ // get post info
    $post_url = explode('-', $_GET['post']);
    $post_uri['id'] = $post_url[0];
}

if($post_uri['id']) {
	$id = $post_uri['id'];
	$cache_key_post = $id;
	$post = $cache->get_item($cache_key_post); // get post from cache
        if(!$post){
            $sql = "SELECT * FROM news WHERE news_id = $id";
            $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
            $result = $connection->query($sql) or die(mysqli_error($connection));
            $post = $result->fetch_assoc();
//		create_cache($cache_key_post, $post, $cache_dir_post); // create section cache;
            $cache->set_item($post); // create post cache;
	}
	
	$view_post = $post ? true : false;
	$section_id = $post['section_id'];
	$category_id = $post['category_id'];
	$issue = false;
	$issue_date = false;
	$post_id = $post['news_id'];
	$post_head_line = $post['head_line'];
	$post_top_shoulder = $post['top_shoulder'];
	$post_bottom_shoulder = $post['bottom_shoulder'];
	$post_publication_date = $post['publication_date'];
	
        if($is_mobile){
            $post_edited = $post['edited'] ? '<i class="fa fa-pencil-square-o w3-small" aria-hidden="true"></i> ' . getBanglaDate($post['edited'], true) . ' ' : "";
        } else {
            $post_edited = $post['edited'] ? 'আপডেট: ' . getBanglaDate($post['edited'], true) . ' | ' : "";
        }
	$post_info = false;
	$post_details = $post['news_details'];
	$post_media = unserialize($post['media']);
	$post_status = $post['news_status'];
	// redirect old url (news_details.php?news_id=136662) to new url (post/136662-xyz)
	if(count($post_url) == 1){
            $redirect_url = SITEURL . '/post/' . $id . '-' . make_alias($post_head_line, $removable_characters);
            header("Location: $redirect_url");
	}
	
	if($post_status == 'published'){ // when post is published
            if($post['issue_date'] && ($post['issue_date'] != '0000-00-00')){ // issue
                $cache_key_issue .= $post['issue_date'];
                $issue_date = $post['issue_date'];
                
                $issue = $cache->get_item($cache_key_issue); // get issue from cache
                if($issue){
                    $sql = "SELECT * FROM publicationdate WHERE publication_date = '" . str_replace('iss_', '', $cache_key_issue) . "'";
                    $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
                    $result = $connection->query($sql) or die(mysqli_error($connection));
                    $num_rows = $result->num_rows;
                    if($num_rows > 0){
                        $issue = $result->fetch_assoc();
//                        create_cache($cache_key_issue, $issue, $cache_dir_issue); // create section cache;
                        $cache->set_item($issue); // create section cache;
                    }
                }
                // check that issue date is published or not
                $view_post = $issue['status'] != 'yes' ? false : true;
            }
            if($view_post){
                if($is_mobile){
                    $post_info = $issue['publication_date'] ? $post_edited  . '<i class="fa fa-calendar w3-small" aria-hidden="true"></i> ' . getBanglaDate($issue['publication_date']) . ' | প্রিন্ট সংস্করণ' : $post_edited . '<i class="fa fa-calendar w3-small" aria-hidden="true"></i> ' . getBanglaDate($post_publication_date, true);
            } else {
                $post_info = $issue['publication_date'] ? $post_edited  . 'প্রকাশিত: ' . getBanglaDate($issue['publication_date']) . ' | প্রিন্ট সংস্করণ' : $post_edited . 'প্রকাশিত: ' . getBanglaDate($post_publication_date, true);
            }
            
            $issue_date = $issue['publication_date'] ? $issue['publication_date'] : false;
            $issue_description = $issue['publication_des'] ? $issue['publication_des'] : false;
            
            $publication_date['id'] = $post['news_pd_id'];
            $page['id'] = $post['news_cat_id'];

            // media for meta tags
            $post_img_defult = "http://www.dailysangram.com/images/the-daily-sangram.png";
            $post_img = FALSE;
            $img_caption = FALSE;
            $img_height = 200;
            $img_width = 200;
            if(!empty($post_media['intro_img_name'])){ // media                        
                $imageFile = './images/'.$post_media['intro_img_name'];                        
                if(file_exists($imageFile) && is_readable($imageFile)){                            
                    $imageSize = getimagesize($imageFile); 
                    $img_height = $imageSize[0];
                    $img_width = $imageSize[1];
                    $post_img = SITEURL . '/images/'.$post_media['intro_img_name'];
                    $img_caption = trim($post_media['intro_img_caption']) ? $post_media['intro_img_caption'] : FALSE;
                }
            }

            $extract = substr(strip_tags($post_details), 0, 800);                        
            $lastSpace = strrpos($extract, ' ');                        
            $news_intro = substr($extract, 0, $lastSpace) . ' ...';
            $post_url = SITEURL . '/?post=' . $post_id . '-' . make_alias($post_head_line, $removable_characters);
            $img_social = $post_img ? $post_img : $post_img_defult;

            // meta description
            $meta_description = $news_intro;

            // facebook_open_graph					
            $facebook_open_graph = true; 
            $title = $post_head_line;
            $og_description = $news_intro;
            $og_url = $post_url;
			
            } else {
                $message = '<p>আপনি যে বিষয়টি খুঁজছেন তা পাওয়া যায়নি। </p><p>আমাদের সাথে থাকার জন্য ধন্যবাদ।</p>';
            }				
	} else {
	    $view_post = false;
	    $message = '<p>আপনি যে বিষয়টি খুঁজছেন তা পাওয়া যায়নি। </p><p>আমাদের সাথে থাকার জন্য ধন্যবাদ।</p>';
	}
}

// max publication date
$max_publication_date = $cache->get_item('max_publication_date'); // get max publication date from cache
//$cache->delete_item('max_publication_date');
if($max_publication_date){
    $rowMaxDate = $max_publication_date;
    $maxDate = $rowMaxDate['publication_date'];
    $publication_date_max = $rowMaxDate['publication_date'];
    $publication_date_max_id = $rowMaxDate['pd_id'];
    $publication_date_max_description = $rowMaxDate['publication_des'];
    $date_line['date'] = $rowMaxDate['publication_date'];
    $date_line['description'] = $rowMaxDate['publication_des'];
} else {
    $sqlMaxDate = "SELECT pd_id, publication_date, publication_des FROM publicationdate WHERE status = 'yes' ORDER BY publication_date DESC LIMIT 1";
    $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
    $resultMaxDate = $connection->query($sqlMaxDate) or die('SQL: ' . $sqlMaxDate . ' ERROR: ' . mysqli_error);
    $rowMaxDate = $resultMaxDate->fetch_assoc();
//    var_dump()
//    create_cache('max_publication_date', $rowMaxDate, $cache_dir_issue); // create issue cache;
    $cache->set_item($rowMaxDate); // create issue cache;
    $maxDate = $rowMaxDate['publication_date'];
    $publication_date_max = $rowMaxDate['publication_date'];
    $publication_date_max_id = $rowMaxDate['pd_id'];
    $publication_date_max_description = $rowMaxDate['publication_des'];
    $date_line['date'] = $rowMaxDate['publication_date'];
    $date_line['description'] = $rowMaxDate['publication_des'];
}
// make menu from daily page
if(array_key_exists('page', $_GET) || $issue_date){ // get page info
	if(array_key_exists('page', $_GET)){
            $page_url = explode('/', $_GET['page']);
            $page['alias'] = $page_url[0];
            $publication_date['date'] = $page_url[1];
	}
        
	if(!$publication_date['date'] && !$issue_date){
		header("Location: ".SITEURL);
	}elseif($publication_date['date'] == $publication_date_max){
        $publication_date['id'] = $publication_date_max_id;
        $publication_date['description'] = $publication_date_max_description;
        $cache_key_daily_page = $publication_date['date'];
    }elseif($publication_date['date'] && !$publication_date['id']){ // get publication details
        $issue = $cache->get_item('iss_' . $publication_date['date']); // get issue from cache
        if($issue){
            $publication_date['id'] = $issue['pd_id'];
            $publication_date['description'] = $issue['publication_des'];
            $date_line['date'] = $issue['publication_date'];
            $date_line['description'] = $issue['publication_des'];
        } else {
            $sql = "SELECT * FROM publicationdate WHERE publication_date = '" . $publication_date['date'] . "'";
            $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
            $result = $connection->query($sql) or die(mysqli_error($connection));
            $num_rows = $result->num_rows;
            if($num_rows > 0){
                $issue = $result->fetch_assoc();
                $publication_date['id'] = $issue['pd_id'];
                $publication_date['description'] = $issue['publication_des'];
                $date_line['date'] = $issue['publication_date'];
                $date_line['description'] = $issue['publication_des'];
                $cache->set_item($issue); // create section cache;
            } else {
                echo 'date not found';
                exit();
            }
        }
        $cache_key_daily_page = $publication_date['date'];
    } elseif($issue_date){

        // query for daily pages
        $date_line['date'] = $issue_date;
        $date_line['description'] = $issue_description;
        $cache_key_daily_page = $issue_date;
    }

    if($cache_key_daily_page){
        $daily_pages = $cache->get_item($cache_key_daily_page); // daily page for cache
        if($daily_pages){
            $sql = "SELECT DISTINCT page_names.cat_id AS id, cat_display_name AS title, alias "
            .   "FROM news, page_names "
            .   "WHERE page_names.cat_id = news.news_cat_id "
            .   "AND news_pd_id = '" .  $publication_date['id'] . "' "
            .   "ORDER BY cat_order ASC";
            $conn = !isset($conn) ? db_connect($user, $pwd, MYDATABASE) : $conn;
                $result = $conn->query($sql);
                $error_info = $conn->errorInfo();
                $query_error = isset($error_info[2]) ? $error_info[2] : false;

                if(!$query_error){
                    foreach($result as $row){
                        if($row['alias'] == $page['alias']){
                            $page['id'] = $row['id'];
                        }
                        $pages[$row['id']] = array('title' => $row['title'], 'alias' => $row['alias']);
//                        $daily_pages[$row['id']] = array('title' => $row['title'], 'alias' => $row['alias']);
                    }
                }
                $cache->set_item($pages); // create daily_pages cache;
        } else {
            foreach($daily_pages as $key => $row){
                if($row['alias'] == $page['alias']){
                    $page['id'] = $key;
                }
                $pages[$key] = array('title' => $row['title'], 'alias' => $row['alias']);
            }
        }
    // ***************************** end daily pages *****************************************
    }
}

$main_menu .= $is_mobile ? '<ul class="w3-ul w3-large">' : '<ul>'; // main menu
$main_menu .= '<li><a href="/"><i class="fa fa-home" aria-hidden="true"></i></a></li>';
$main_menu .= $publication_date['id'] ? '<li><a class="active" href="/page/first-page/' . $publication_date_max . '">আজকের পত্রিকা</a></li>' : '<li><a href="/page/first-page/' . $publication_date_max . '">আজকের পত্রিকা</a></li>';
foreach($sections as $key => $value){ 
    $main_menu .= "<li>";
    if(($section['alias'] == $value['alias']) || (isset($section_id) && $section_id == $key)){ // get active section id
        $section['id'] = $key;
        $main_menu .= '<a class="active" href="/section/'. $value['alias'] . '">' . $value['title'] . '</a>';
    } else{
        $main_menu .= '<a href="/section/'. $value['alias'] . '">' . $value['title'] . '</a>';
    }
    $main_menu .= "</li>";
}
$main_menu .= '</ul>';

// breadcrumbs
$breadcrumbs .= '<ul class="breadcrumb">';
$breadcrumbs .= '<li><a href="/">প্রচ্ছদ</a></li>';

// main sub menu
if($section['id'] && !$post_uri['id']){
	$main_sub_menu .= '<ul class="subMenu">';
	$breadcrumbs .= '<li class="bcrmSeparator">';
	$breadcrumbs .= '<span class="bcrmSeparatorInner">›</span>';
	$breadcrumbs .= '</li>';
	$breadcrumbs .= '<li>';
	$breadcrumbs .= '<a href="/section/' . $sections[$section['id']]['alias'].'">'. $sections[$section['id']]['title'].'</a>';
	$breadcrumbs .= '</li>';
	foreach($categories[$section['id']] as $key => $value){
		if($category['alias'] == $value['alias']){
			$category['id'] = $value['id'];
			$breadcrumbs .= '<li class="bcrmSeparator">';
			$breadcrumbs .= '<span class="bcrmSeparatorInner">›</span>';
			$breadcrumbs .= '</li>';
			$breadcrumbs .= "<li>";
			$breadcrumbs .= '<a href="/section/'. $sections[$section['id']]['alias'] .'/'. $value['alias'] . '">' . $value['title'] . '</a>';
			$breadcrumbs .= "</li>";
		} else {
			$main_sub_menu .= "<li>";
			$main_sub_menu .= '<a href="/section/'. $sections[$section['id']]['alias'] .'/'. $value['alias'] . '">' . $value['title'] . '</a>';
			$main_sub_menu .= "</li>";
		}
	}
	$main_sub_menu .= '</ul>';

    if($is_mobile){
        $sub_sections = $category['alias'] ?    '<option value="/section/' . $sections[$section['id']]['alias'] . '">' . $sections[$section['id']]['title'] . '</option>' :
                                                '<option value="" disabled selected>' . $sections[$section['id']]['title'] . '</option>' ;
        foreach($categories[$section['id']] as $key => $value){
            if($category['alias'] == $value['alias']){
                $sub_sections .= '<option value="/section/' . $sections[$section['id']]['alias'] . '/' . $value['alias'] . '" disabled selected>' . $sections[$section['id']]['title'] . ' › ' . $value['title'] . '</option>';
            } else {
                $sub_sections .= '<option value="/section/' . $sections[$section['id']]['alias'] . '/' . $value['alias'] . '">' . $value['title'] . '</option>';
            }

        }
    }
}elseif($publication_date['id'] || $issue_date) {
	$print_date = isset($issue_date) ? $issue_date : $publication_date['date'];
	$print_label = $print_date < $publication_date_max ? 'আর্কাইভ' : 'আজকের পত্রিকা';
	$main_sub_menu .= '<ul class="subMenu">';
	$breadcrumbs .= '<li class="bcrmSeparator">';
	$breadcrumbs .= '<span class="bcrmSeparatorInner">›</span>';
	$breadcrumbs .= '</li>';
	$breadcrumbs .= '<li><a href="/page/first-page/' . $print_date . '">' . $print_label . '</a></li>';
	if($print_label == 'আর্কাইভ'){
        $breadcrumbs .= '<li class="bcrmSeparator">';
        $breadcrumbs .= '<span class="bcrmSeparatorInner">›</span>';
        $breadcrumbs .= '</li>';
        $breadcrumbs .= '<li><a href="/page/first-page/' . $print_date . '">' . getBanglaDate($print_date, false, false) . '</a></li>';
    }
	foreach($pages as $key => $value){
		if($key == $page['id']){
			$breadcrumbs .= '<li class="bcrmSeparator">';
			$breadcrumbs .= '<span class="bcrmSeparatorInner">›</span>';
			$breadcrumbs .= '</li>';
			$breadcrumbs .= "<li>";
			$breadcrumbs .= '<a href="/page/'. $value['alias'] .'/'. $print_date . '">' . $value['title'] . '</a>';
			$breadcrumbs .= "</li>";
		} else {
			$main_sub_menu .= "<li>";
			$main_sub_menu .= '<a href="/page/'. $value['alias'] .'/'. $print_date . '">' . $value['title'] . '</a>';
			$main_sub_menu .= "</li>";
		}
	}
	$main_sub_menu .= '</ul>';
	
    if($is_mobile){
        $print_label = $print_date < $publication_date_max ? 'আর্কাইভ › ' . getBanglaDate($print_date, false, false) : 'আজকের পত্রিকা';
        foreach($pages as $key => $value){
            if($key == $page['id']){
                if($view_post){
                    $sub_sections .= '<option value="/page/' . $value['alias'] . '/' . $print_date . '">' . $value['title'] . '</option>';
                    $fst_sub_sections = '<option value="/page/' . $value['alias'] . '/' . $print_date . '" disabled selected>' . $print_label . ' › ' . $value['title'] . '</option>';
                } else {
                    $sub_sections .= '<option value="/page/' . $value['alias'] . '/' . $print_date . '" disabled selected>' . $print_label . ' › ' . $value['title'] . '</option>';
                }
            } else {
                $sub_sections .= '<option value="/page/' . $value['alias'] . '/' . $print_date . '">' . $value['title'] . '</option>';
            }
        }
    }
}elseif($post_uri['id'] && !$issue_date){
	$main_sub_menu .= '<ul class="subMenu">';
	$breadcrumbs .= '<li class="bcrmSeparator">';
	$breadcrumbs .= '<span class="bcrmSeparatorInner">›</span>';
	$breadcrumbs .= '</li>';
	$breadcrumbs .= '<li>';
	$breadcrumbs .= '<a href="/section/' . $sections[$section_id]['alias'] .'">'. $sections[$section_id]['title'] .'</a>';
	$breadcrumbs .= '</li>';
	foreach($categories[$section_id] as $key => $value){
		if($category_id == $value['id']){
			$breadcrumbs .= '<li class="bcrmSeparator">';
			$breadcrumbs .= '<span class="bcrmSeparatorInner">›</span>';
			$breadcrumbs .= '</li>';
			$breadcrumbs .= "<li>";
			$breadcrumbs .= '<a href="/section/'. $sections[$section_id]['alias'] .'/'. $value['alias'] . '">' . $value['title'] . '</a>';
			$breadcrumbs .= "</li>";
		} else {
			$main_sub_menu .= "<li>";
			$main_sub_menu .= '<a href="/section/'. $sections[$section_id]['alias'] .'/'. $value['alias'] . '">' . $value['title'] . '</a>';
			$main_sub_menu .= "</li>";
		}
	}
	$main_sub_menu .= '</ul>';

    if($is_mobile){
        $sub_sections = '<option value="/section/' . $sections[$section['id']]['alias'] . '">' . $sections[$section['id']]['title'] . '</option>';
        foreach($categories[$section['id']] as $key => $value){
            if($category_id == $value['id']){
                if($view_post){
                    $sub_sections .= '<option value="/section/' . $sections[$section['id']]['alias'] . '/' . $value['alias'] . '">' . $value['title'] . '</option>';
                    $fst_sub_sections = '<option value="" disabled selected>' . $sections[$section['id']]['title'] . ' › ' . $value['title'] . '</option>';
                } else {
                    $sub_sections .= '<option value="" disabled selected>' . $sections[$section['id']]['title'] . ' › ' . $value['title'] . '</option>';
                }
            } else {
                $sub_sections .= '<option value="/section/' . $sections[$section['id']]['alias'] . '/' . $value['alias'] . '">' . $value['title'] . '</option>';
            }
        }
    }
}
$breadcrumbs .= '</ul>';

// assigning page name
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// #################              queries for news details page           #####################

if($currentPage == 'post.php'){
    $id = get_id($_REQUEST['news_id'], '-');
    if($id){

        $this_time = date('Y-m-d H:i:s');
        $newsId = $id;
        // query for details news
        $sqlNewsDetails = "
            SELECT
            news.news_id AS news_id,
            head_line,
            top_shoulder,
            news_details,
            media,
            news_pd_id,
            news.created AS created,
            cat_display_name,
            section.title AS section_title
            FROM news
            LEFT JOIN page_names ON news.news_cat_id = page_names.cat_id
            LEFT JOIN section ON news.section_id = section.id
            WHERE ((publication_date <= '2014-11-12 12:18:23' AND publication_date != '0000-00-00 00:00:00') OR news_pd_id IS NOT NULL)
            AND news_status = 'published'
            AND news_id = $newsId";
        $resultNewsDetails = $connection->query($sqlNewsDetails) or die(mysqli_error($connection));
        $rowNewsDetails = $resultNewsDetails->fetch_assoc();
        $rowNewsDetails['media'] = unserialize($rowNewsDetails['media']);
        // date line
        if(!empty($rowNewsDetails['news_pd_id'])){
            $sql_date_line = "
                SELECT
                publication_date,
                publication_des
                FROM publicationdate
                WHERE pd_id = {$rowNewsDetails['news_pd_id']}
                LIMIT 1;
            ";
        } else {
            $sql_date_line = "
                SELECT
                publication_date,
                publication_des
                FROM publicationdate
                WHERE status = 'yes'
                ORDER BY pd_id DESC
                LIMIT 1;
            ";
        }
        $result_date_line = $connection->query($sql_date_line) or die(mysqli_error($connection));
        $row_date_line = $result_date_line->fetch_assoc();

        // assigning publication date from database
        if($rowNewsDetails){
            $publicationDate = $row_date_line['publication_date'];
            $publicationDes = $row_date_line['publication_des'];
            $createDate = $rowNewsDetails['created'];
            // query for menu
            $sqlMenu = "SELECT DISTINCT cat_display_name, cat_name FROM news, page_names, page_section, publicationdate WHERE page_names.cat_id = news.news_cat_id AND page_names.cat_sec_id = page_section.sec_id AND publicationdate.pd_id = news.news_pd_id AND publicationdate.publication_date = '$publicationDate' AND publication_des = '$publicationDes' ORDER BY sec_order, cat_order";
            $resultMenu = $connection->query($sqlMenu) or die(mysqli_error());
            
            $readNewValue = $rowNewsDetails['read_num']+1;
            
            // new connection for update read_num field
            $connectionWrite = @dbConnect('write', MYDATABASE);
            // update read_num field
//            $sqlNewsUpdate = "UPDATE news SET read_num = $readNewValue, created = '$createDate'  WHERE news_id = $newsId";
//            $resultNewsUpdate = @$connectionWrite->query($sqlNewsUpdate) or @die(mysqli_error());
            // close newly opend connection
            @mysqli_close($connectionWrite);
        } else {
//            header("Location: ".SITEURL."/system_error.php?errortype=newsdetails");
        }
        
    } else {
        header("Location: ".SITEURL."/system_error.php?errortype=newsdetails");
    }
}

if($currentPage == 'news_details.php'){
    echo '<p>আপনি যে বিষয়টি খুঁজছেন তা পাওয়া যায়নি। </p><p>আমাদের সাথে থাকার জন্য ধন্যবাদ।</p>';
    exit();
    if(isset ($_GET['news_id']) && is_numeric($_GET['news_id'])) {

        $id = $_GET['news_id'];
        header("Location: /post/$id"); // redirect to new url
    }
}

// #################              queries for others page           #####################
if($currentPage != 'news_details.php' && !$section['alias']){
    // assigning publication date
    if(isset($_GET['publicationdate'])){
        $dateFromUrl = strtotime(str_replace('-', '', $_GET['publicationdate']));
        if($dateFromUrl){
            $publicationDate = date("Y-m-d", $dateFromUrl);
            if($publicationDate < '2009-03-20'){
                $publicationDes = 'NULL';
            } else {
                $publicationDes = $_GET['publicationdes'];
            }
        } 
    } else {
//        var_dump($issue);
//        $publicationDate = $rowMaxDate['publication_date'];
//        $publicationDes = $rowMaxDate['publication_des'];
    }
    
    // query for menu
//    $sqlMenu = "SELECT 
//    DISTINCT cat_display_name, 
//    cat_name 
//    FROM news, page_names, page_section, publicationdate 
//    WHERE page_names.cat_id = news.news_cat_id 
//    AND page_names.cat_sec_id = page_section.sec_id 
//    AND publicationdate.pd_id = news.news_pd_id 
//    AND news_status = 'published'
//    AND publicationdate.publication_date = '$publicationDate' ORDER BY sec_order, cat_order";
	if(isset($connection)){
		// $resultMenu = $connection->query($sqlMenu) or die(mysqli_error());
		// $numRows = $resultMenu->num_rows;
		// if($numRows < 1){
			// header("Location: ".SITEURL);
		// }
	}
}
// #################              queries for news_list page           #####################
if($currentPage == 'news_list.php'){
    echo '<p>আপনি যে বিষয়টি খুঁজছেন তা পাওয়া যায়নি। </p><p>আমাদের সাথে থাকার জন্য ধন্যবাদ।</p>';
    exit();
    if(array_key_exists('catdisplayname', $_GET) && array_key_exists('publicationdate', $_GET) && array_key_exists('publicationdes', $_GET)){

    } else {
        header("Location: ".SITEURL."/system_error.php?errortype=newslist");
    }
}
// #################              queries for related_articles page           #####################
if($currentPage == 'related_articles.php'){
    if((array_key_exists('topicid', $_GET) && array_key_exists('topic', $_GET) && array_key_exists('publicationdate', $_GET) && array_key_exists('publicationdes', $_GET))
        || (array_key_exists('topicid', $_GET) && array_key_exists('topic', $_GET) && array_key_exists('publicationdate', $_GET) && array_key_exists('publicationdes', $_GET) && array_key_exists('page', $_GET)))
    {
        $topicsId = $_GET['topicid'];
        $topic = $_GET['topic'];
        
        $items = 10;
        $page = 1;
        if(isset($_GET['page']) and is_numeric($_GET['page']) and $page = $_GET['page'])
            $limit = " LIMIT ".(($page-1)*$items).",$items";
        else
            $limit = " LIMIT $items";
        $sqlNewsTopics = "SELECT news.news_id AS news_id, head_line, top_shoulder, news_details, read_num, photo_name, news_topits_id, topits FROM publicationdate, related_toptis, news LEFT JOIN photos ON news.news_id = photos.news_id WHERE publicationdate.pd_id = news.news_pd_id AND news.news_topits_id = related_toptis.topits_id AND news_topits_id = '$topicsId' AND topits = '$topic' AND news_status = 'published' ORDER BY news_id DESC";
        $sqlStrAux = "SELECT count(news.news_id) as total FROM news, related_toptis WHERE news.news_topits_id = related_toptis.topits_id AND news_topits_id = '$topicsId' AND topits = '$topic' AND news_status = 'published'";
        $resultStrAux = $connection->query($sqlStrAux);
        $aux = $resultStrAux->fetch_assoc();
        $resultNewsTopics = $connection->query($sqlNewsTopics.$limit);
        
//        $sqlNewsTopics = "SELECT news.news_id AS news_id, head_line, top_shoulder, news_details, read_num, photo_name, news_topits_id, topits FROM publicationdate, related_toptis, news LEFT JOIN photos ON news.news_id = photos.news_id WHERE publicationdate.pd_id = news.news_pd_id AND news.news_topits_id = related_toptis.topits_id AND news_topits_id = '$topicsId' AND topits = '$topic' AND news_status = 'published' ORDER BY news_id DESC";
//        $resultNewsTopics = $connection->query($sqlNewsTopics) or die(mysqli_error());
        $numRows = $resultNewsTopics->num_rows;
        if($numRows < 1){
            header("Location: ".SITEURL."/system_error.php?errortype=newslist");
        }
    } else {
        header("Location: ".SITEURL."/system_error.php?errortype=newslist");
    }
}

// #################              queries for index page           #####################
if($currentPage == 'index.php'){

    // Latest news
	$latest_news = $cache->get_item($cache_key_latest_news); // get last update from cache
	if(!$latest_news){
		$this_time = date('Y-m-d H:i:s');
		$time_before_6hr = date_format(date_sub(new DateTime(), new DateInterval('PT6H')), 'Y-m-d H:i:s');
		$sql_last_update = "
			SELECT
			news.news_id AS news_id,
			head_line,
			top_shoulder,
			publication_date
			FROM news
			WHERE news_status = 'published'
			AND publication_date != '0000-00-00 00:00:00'
			AND news_pd_id = 0
			ORDER BY publication_date DESC LIMIT 10
			";
		$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
		$result_last_update = $connection->query($sql_last_update) or die(mysqli_error($connection));
		// create cache
		while($rows = $result_last_update->fetch_assoc()){$latest_news[] = $rows;}
		$cache->set_item($latest_news); // create cache;
	}
	
	if(empty($_GET)){
		// home lead
		$home_lead = $cache->get_item('home_lead'); // get home lead from cache

                if(!$home_lead){
			$sqlHomeLead = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND block_id = 1
				ORDER BY news.news_id DESC
				LIMIT 1";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultHomeLead = $connection->query($sqlHomeLead) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultHomeLead->fetch_assoc()){$home_lead[] = $rows;}
			$cache->set_item($home_lead); // create cache;
		}

		// second lead
		$second_lead = $cache->get_item('second_lead'); // get home second lead cache
		if(!$second_lead){
			$sqlSecondLead = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND block_id = 2
				ORDER BY news.news_id DESC
				LIMIT 3";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultSecondLead = $connection->query($sqlSecondLead) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultSecondLead->fetch_assoc()){$second_lead[] = $rows;}
			$cache->set_item($second_lead); // create cache;
		}
		
		// Feature lead
		$feature_lead = $cache->get_item('feature_lead'); // get home feature lead cache
		if(!$feature_lead){
			$sqlFeatureLead = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND block_id = 3
				ORDER BY news.news_id DESC
				LIMIT 3";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultFeatureLead = $connection->query($sqlFeatureLead) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultFeatureLead->fetch_assoc()){$feature_lead[] = $rows;}
			$cache->set_item($feature_lead); // create cache;
		}
		
		
		// Bangladesh lead
		$bangladesh_lead = $cache->get_item('bangladesh_lead'); // get home Bangladesh lead cache
		if(!$bangladesh_lead){
			$sqlBangladeshLead = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 1
				AND block_id = 4
				ORDER BY news.news_id DESC
				LIMIT 1";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultBangladeshLead = $connection->query($sqlBangladeshLead) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultBangladeshLead->fetch_assoc()){$bangladesh_lead[] = $rows;}
			$cache->set_item($bangladesh_lead); // create cache;
		}
		
		// Bangladesh others
		$bangladesh_others = $cache->get_item('bangladesh_others'); // get home bangladesh others cache
		if(!$bangladesh_others){
			$sqlBangladeshOthers = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 1
				AND block_id = 0
				ORDER BY news.news_id DESC
				LIMIT 4";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultBangladeshOthers = $connection->query($sqlBangladeshOthers) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultBangladeshOthers->fetch_assoc()){$bangladesh_others[] = $rows;}
			$cache->set_item($bangladesh_others); // create cache;
		}
		
		// International
		$international = $cache->get_item('international_home'); // get home international cache
		if(is_null($international)){
			$sqlInternational = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 2
				AND block_id = 0
				ORDER BY news.news_id DESC
				LIMIT 4";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultInternational = $connection->query($sqlInternational) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultInternational->fetch_assoc()){$international[] = $rows;}
			$cache->set_item($international);
		}
	
		// sports
		$sports = $cache->get_item('sports_home'); // get home sports cache
		if(!$sports){
			$sqlSports = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 3
				AND block_id = 0
				ORDER BY news.news_id DESC
				LIMIT 4";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultSports = $connection->query($sqlSports) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultSports->fetch_assoc()){$sports[] = $rows;}
			$cache->set_item($sports); // create cache;
		}

		
		// Life style lead
		$life_style_lead = $cache->get_item('life_style_lead'); // get home life style lead cache
		if(!$life_style_lead){
			$sqlLifeStyleLead = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 11
				AND block_id = 4
				ORDER BY news.news_id DESC
				LIMIT 1";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultLifeStyleLead = $connection->query($sqlLifeStyleLead) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultLifeStyleLead->fetch_assoc()){$life_style_lead[] = $rows;}
			$cache->set_item($life_style_lead); // create cache;
		}
		
		// Life style others
		$life_style_others = $cache->get_item('life_style_others'); // get home life style others cache
		if(!$life_style_others){
			$sqlLifeStyleOthers = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 11
				AND block_id = 0
				ORDER BY news.news_id DESC
				LIMIT 4";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultLifeStyleOthers = $connection->query($sqlLifeStyleOthers) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultLifeStyleOthers->fetch_assoc()){$life_style_others[] = $rows;}
			$cache->set_item($life_style_others);
		}

		// feature
		$feature = $cache->get_item('feature_home'); // get home feature cache
		if(!$feature){
			$sqlFeature = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 9
				AND block_id = 0
				ORDER BY news.news_id DESC
				LIMIT 4";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultFeature = $connection->query($sqlFeature) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultFeature->fetch_assoc()){$feature[] = $rows;}
			$cache->set_item($feature); // create cache;
		}
		
		
		// science-technology
		$science_technology = $cache->get_item('science_technology_home'); // get home science-technology cache
		if(!$science_technology){
			$sqlScienceTechnology = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 10
				AND block_id = 0
				ORDER BY news.news_id DESC
				LIMIT 4";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultScienceTechnology = $connection->query($sqlScienceTechnology) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultScienceTechnology->fetch_assoc()){$science_technology[] = $rows;}
			$cache->set_item($science_technology); // create cache;
		}
		
		
		// opinion
		$opinion = $cache->get_item('opinion_home'); // get home opinion cache
		if(!$opinion){
			$sqlOpinion = "
				SELECT
				news.news_id AS news_id,
				head_line,
				top_shoulder,
				news_details,
				publication_date,
				media
				FROM news
				WHERE news_status = 'published'
				AND front_page = 'yes'
				AND section_id = 6
				AND block_id = 0
				ORDER BY news.news_id DESC
				LIMIT 5";
			$connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
			$resultOpinion = $connection->query($sqlOpinion) or die(mysqli_error($connection));
			// create cache
			while($rows = $resultOpinion->fetch_assoc()){$opinion[] = $rows;}
			$cache->set_item($opinion); // create cache;
		}
	}

    if ($section['alias']) {
        $sec_or_cat = $category['id'] ? "category_id = " . $category['id'] : " section_id = " . $section['id'];
        $cache_key_section_page = $category['alias'] ? $section['alias'] . '_' . $category['alias'] : $section['alias'];
        $cache_key_section_page_count = $cache_key_section_page . '_count';

        $number_of_section = $cache->get_item($cache_key_section_page_count); // get cache
        if (!$number_of_section) {
            $sql = "SELECT COUNT(*) AS number_of_section 
                FROM news
                WHERE news_status = 'published'
                AND $sec_or_cat";
            $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
            $result = $connection->query($sql) or die(mysqli_error($connection));
            $row = $result->fetch_assoc();
            $number_of_section = $row['number_of_section'];
            $cache->set_item($number_of_section); // create cache;
        }

        $item_per_page = 11;
        $show_link_num = $is_mobile ? 6 : 12;
        $show_link_mid_num = ceil($show_link_num / 2);
        $total_pages = (ceil($number_of_section / $item_per_page));
        $record_start = $page_no > 1 ? (($page_no - 1) * $item_per_page) + 1 : 0;
        $record_end = $page_no > 1 ? ($record_start + $item_per_page) - 1 : $item_per_page;

        $pagination = $number_of_section > $item_per_page ? true : false;
        $link_start = $page_no > $show_link_mid_num ? $page_no - ($show_link_mid_num - 1) : 1;
        $link_end = $page_no > $show_link_mid_num ? $page_no + $show_link_mid_num : $show_link_num;
        $previous = $page_no > 1 ? $page_no - 1 : false;
        $next = $page_no < $total_pages ? $page_no + 1 : false;
        $first_page = $page_no > 1 ? 1 : false;
        $last_page = $page_no < $total_pages ? $total_pages : false;

        if($page_no > 1){
            $sqlNewsFront = "SELECT
            news.news_id AS news_id,
            head_line,
            top_shoulder,
            news_details,
            publication_date,
            media,
            created
            FROM news
            WHERE news_status = 'published'
            AND $sec_or_cat
            ORDER BY publication_date DESC LIMIT $record_start, $item_per_page";
            $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;

            $resultPage = $connection->query($sqlNewsFront) or die(mysqli_error($connection));
            while ($rows = $resultPage->fetch_assoc()) {
                $posts[] = $rows;
            }
        } else {
            $posts = $cache->get_item($cache_key_section_page); // get cache
            if (!$posts) {
                $sqlNewsFront = "SELECT
            news.news_id AS news_id,
            head_line,
            top_shoulder,
            news_details,
            publication_date,
            media,
            created
            FROM news
            WHERE news_status = 'published'
            AND $sec_or_cat
            ORDER BY publication_date DESC LIMIT $record_start, $item_per_page";
                $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;

                $resultPage = $connection->query($sqlNewsFront) or die(mysqli_error($connection));
                while ($rows = $resultPage->fetch_assoc()) {
                    $posts[] = $rows;
                }
                $cache->set_item($posts); // create cache;
            }
        }
    } elseif (!($page['alias'])) {
        if ($_POST && array_key_exists('archvesearch', $_POST)) {
            if (is_numeric($_POST['archiveyear']) && is_numeric($_POST['archivemonth']) && is_numeric($_POST['archiveday'])) {
                $archiveDate = $_POST['archiveyear'] . '-' . $_POST['archivemonth'] . '-' . $_POST['archiveday'];

                if ($archiveDate > $maxDate) {
                    $archivemessage = 'দুঃখিত!! আপনার নির্বাচিত “' . getBanglaDate($archiveDate) . '” তারিখটি ভবিষ্যতের একটি দিন। পুরোনো দিনের পত্রিকা দেখতে চাইলে আজ থেকে পেছনের যে কোন একটি তারিখ নির্বাচন করে আবার চেষ্টা করুন। আমাদের সাথে থাকার জন্য আপনাকে আন্তরিক ধন্যবাদ।';
                } elseif ($archiveDate == $maxDate) {
                    $archivemessage = 'দুঃখিত! আপনি আজকের তারিখ নির্বাচন করেছেন। পুরোনো সংখ্যা দেখতে চাইলে আজ থেকে পেছনের যে কোন একটি তারিখ নির্বাচন করে আবার চেষ্টা করুন। আমাদের সাথে থাকার জন্য আপনাকে আন্তরিক ধন্যবাদ।';
                } else {
                    $sqlPublicationDate = "SELECT publication_date, publication_des FROM publicationdate WHERE status = 'yes' AND publication_date = '$archiveDate'";
                    $resultPublicationDate = $connection->query($sqlPublicationDate) or die(mysqli_error);
                    $rowPublicationDate = $resultPublicationDate->fetch_assoc();
                    if ($rowPublicationDate) {
                        $publicationDate = $rowPublicationDate['publication_date'];
                        $publicationDes = $rowPublicationDate['publication_des'];
                    } else {
                        $archivemessage = 'দুঃখিত!! ' . getBanglaDate($archiveDate) . ' তারিখের সংখ্যাটি আমাদের আর্কাইভে নেই। অথবা কোন কারণে এটি প্রকাশ করা হয়নি। আমাদের সাথে থাকার জন্য আপনাকে আন্তরিক ধন্যবাদ।';
                    }
                }
            } else {
                $archivemessage = 'দুঃখিত!! সঠিকভাবে তারিখ নির্বাচন করা হয়নি। অনুগ্রহ করে যে কোন তারিখ নির্বাচন করুন এবং আবার চেষ্টা করুন ...';
            }
            $sqlNewsFront = "SELECT
            news.news_id AS news_id,
            head_line,
            top_shoulder,
            news_details,
            read_num,
            media,
            news_topits_id, topits
            FROM publicationdate, news
            LEFT JOIN related_toptis ON news.news_topits_id = related_toptis.topits_id
            WHERE publicationdate.pd_id = news.news_pd_id
            AND front_page = 'yes'
            AND news_status = 'published'
            AND publicationdate.publication_date = '$publicationDate'
            ORDER BY news_order ASC";
            $resultNewsFront = $connection->query($sqlNewsFront) or die(mysqli_error());

            // topics of the day
            $topicId = array();
            while ($rowsTopicsId = $resultNewsFront->fetch_assoc()) {
                if ($rowsTopicsId['news_topits_id'] != NULL && $rowsTopicsId['news_topits_id'] != 0 && !in_array($rowsTopicsId['news_topits_id'], $topicId)) {
                    $topicId[] = $rowsTopicsId['news_topits_id'];
                    $topicNewsId[] = 'news_topits_id = ' . $rowsTopicsId['news_topits_id'];
                }
            }

            if (count($topicId) > 0) {
                $topicWhereCondition = implode(' OR ', $topicNewsId);
                $sqlHeadlineForTopics = "SELECT news_id, head_line, news_topits_id FROM news WHERE $topicWhereCondition AND news_status = 'published' ORDER BY news_id DESC";
                $resultHeadlineForTopics = $connection->query($sqlHeadlineForTopics) or die(mysqli_error());
                $numHeadlineForTopics = $resultHeadlineForTopics->num_rows;
                if ($numHeadlineForTopics > 0) {
                    while ($rowsHeadlineForTopics = $resultHeadlineForTopics->fetch_assoc()) {
                        $newsidForTopics[] = $rowsHeadlineForTopics['news_id'];
                        $allTopicsId[] = $rowsHeadlineForTopics['news_topits_id'];
                    }
                }
                $allTopicsAsString = implode(" ", $allTopicsId);
            }
            mysqli_data_seek($resultNewsFront, 0);

            // query for menu
            $sqlMenu = "SELECT DISTINCT cat_display_name, cat_name FROM news, page_names, page_section, publicationdate WHERE page_names.cat_id = news.news_cat_id AND page_names.cat_sec_id = page_section.sec_id AND publicationdate.pd_id = news.news_pd_id AND publicationdate.publication_date = '$publicationDate' AND publication_des = '$publicationDes' ORDER BY sec_order, cat_order";
            $resultMenu = $connection->query($sqlMenu) or die(mysqli_error());
            $numRows = $resultMenu->num_rows;
            if ($numRows < 1) {
//                header("Location: " . SITEURL);
            }
        } elseif ((array_key_exists('publicationdate', $_GET) && array_key_exists('publicationdes', $_GET))) {

            // Latest news
            // $this_time = date('Y-m-d H:i:s');
            // $time_before_6hr = date_format(date_sub(new DateTime(), new DateInterval('PT6H')), 'Y-m-d H:i:s');
            // $sql_last_update = "
            // SELECT
            // news.news_id AS news_id,
            // head_line,
            // top_shoulder,
            // publication_date
            // FROM news
            // WHERE news_status = 'published'
            // AND publication_date != '0000-00-00 00:00:00'
            // AND news_pd_id = 0
            // ORDER BY publication_date DESC LIMIT 10
            // ";
            // $result_last_update = $connection->query($sql_last_update) or die(mysqli_error($connection));
        } else {
            if ($_SERVER['REMOTE_ADDR'] == '27.147.172.52' || $_SERVER['REMOTE_ADDR'] == '103.71.40.22' || $_SERVER['REMOTE_ADDR'] == '103.239.255.155' || $_SERVER['REMOTE_ADDR'] == '202.164.211.165') {
                // var_dump($_GET);
            } else {
                // header("Location: ".SITEURL."/system_error.php?errortype=index");
            }
        }
    } else {
//        $cache_key_page = $page['alias'] . '_' . $publication_date['date'];
        $cache_dir_page = $cache_dir_page . $publication_date['date'] . '/';
//        $cache_key_page = $page['alias'] . '_' . $publication_date['date'];
        $cache_key_page = $page['alias'] . '_' . $publication_date['date'];
        $posts = $cache->get_item($cache_key_page); // get cache
        if (!$posts) {
            $sql = "SELECT
            news.news_id AS news_id,
            head_line,
            top_shoulder,
            news_details,
            media
            FROM news
            WHERE news_status = 'published'
            AND news_pd_id = " . $publication_date['id'] . "
            AND news_cat_id = " . $page['id'] . "
            ORDER BY publication_date ASC";
            $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
            $resultPage = $connection->query($sql) or die('SQL: ' . $sql . ' Error: ' . mysqli_error($connection));
            while ($rows = $resultPage->fetch_assoc()) {
                $posts[] = $rows;
            }
            $cache->set_item($posts); // create cache;
        }

    }
}

// #################              queries for print page           #####################
if($currentPage == 'print.php'){
    if(isset ($_GET['news_id']) && is_numeric($_GET['news_id'])){
        // convert news_id into integer
        $newsId = (int)$_GET['news_id'];
        
        // query for details news
        if(count($_GET) == 1){
            $sqlNewsDetails = "SELECT head_line, top_shoulder, news_details, publicationdate.publication_date AS publication_date, publication_des, cat_display_name, photo_name, caption FROM publicationdate, page_names, news LEFT JOIN photos ON news.news_id = photos.news_id WHERE publicationdate.pd_id = news.news_pd_id AND news.news_cat_id = page_names.cat_id AND news_status = 'published' AND publicationdate.status = 'yes' AND news.news_id = $newsId";
            $resultNewsDetails = $connection->query($sqlNewsDetails) or die(mysqli_error());
            $rowNewsDetails = $resultNewsDetails->fetch_assoc();
        } else {
            header("Location: ".SITEURL);
        }
        
        // assigning publication date from database
        if($rowNewsDetails){
            $publicationDate = $rowNewsDetails['publication_date'];
            $publicationDes = $rowNewsDetails['publication_des'];
        } else {
            header("Location: ".SITEURL."/system_error.php?errortype=print");
        }
        
    } else {
        header("Location: ".SITEURL);
    }
}
// #################              queries for sangram_rss page           #####################
if($currentPage == 'sangram_rss.php'){
    $posts = $cache->get_item($cache_key_rss); // cache
    if(!$posts) {
        $sqlRss = "SELECT news.news_id AS news_id, head_line, news_details, photo_name, created FROM publicationdate, news LEFT JOIN photos ON news.news_id = photos.news_id WHERE publicationdate.pd_id = news.news_pd_id AND front_page = 'yes' AND news_status = 'published' AND publicationdate.publication_date = '$publication_date_max' ORDER BY news_order ASC";
        $connection = !isset($connection) ? dbConnect('read', MYDATABASE) : $connection;
        $resultRss = $connection->query($sqlRss) or die(mysqli_error());

        if($resultRss->num_rows > 0){
            while($rows = $resultRss->fetch_assoc()){$posts[] = $rows;}
            $cache->set_item($posts); // create cache;
        }
    }
}
// #################              queries for system_error page           #####################
if($currentPage == 'system_error.php'){
    if((array_key_exists('errortype', $_GET)) && (in_array('index', $_GET) || in_array('newslist', $_GET) || in_array('newsdetails', $_GET))){
        $errorMessage = 'দুঃখিত! আপনি সম্ভবত এমন একটি বিষয় খুঁজছেন যা এই সাইট সম্পর্কিত নয়। যদি আপনি কোন সংবাদ বা কোন লেখা খুঁজতে চান তবে সংশ্লিষ্ট শব্দ নিচের সার্চ বক্সে লিখে সার্চ করুন... ...';
    } else {
        header("Location: ".SITEURL);
    }
}