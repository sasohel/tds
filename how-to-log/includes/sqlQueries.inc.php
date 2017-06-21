<?php
date_default_timezone_set('Asia/Dhaka');
include_once('../system/system_config.php');
include_once('../system/library/simple_php_cache/cache.class.php'); // include simple php cache

$connection = dbConnect('read', MYDATABASE);
$cache_dir_rss = '../' . $cache_directory . 'rss/';
$cache_key_rss = 'rss_print';
$cache_dir_daily_page = '../' . $cache_directory . 'daily_pages/';
$cache_key_daily_page = false;
$cache_dir_section = '../' . $cache_directory . 'section/';
$cache_key_section = 'sections';
$cache_dir_section_page = '../' . $cache_directory . 'section_page/';
$cache_key_section_page = false;
$cache_dir_category = '../' . $cache_directory . 'category/';
$cache_key_category = 'categories';
$cache_dir_issue = '../' . $cache_directory . 'issue/';
$cache_key_issue = '';
$cache_dir_post = '../' . $cache_directory . 'posts/';
$cache_key_post = '';
$cache_dir_latest_news = '../' . $cache_directory . 'latest_news/';
$cache_key_latest_news = 'latest_news';
$cache_dir_home = '../' . $cache_directory . 'home/';
$post_categories = array();

if($_SERVER['REMOTE_ADDR'] == '27.147.172.52' ){
	
}

// assigning page name
$currentPage = basename($_SERVER['SCRIPT_NAME']);


/*  ########################### reusable queries ####################################### */
// query for section
$sections = get_cache($cache_key_section, $cache_dir_section); // sections for cache
if(!$sections) {
    $sql = "SELECT id, title, alias FROM section ORDER BY 'order' ASC";
    $result = $connection->query($sql) or die(mysqli_error($connection));
    while ($rows = $result->fetch_assoc()) {
        $sections[$rows['id']] = array('title' => $rows['title'], 'alias' => $rows['alias']);
    }
    create_cache($cache_key_section, $sections, $cache_dir_section); // create section cache;
} // ***************************** end section *****************************************

// query for category
$categories = get_cache($cache_key_category, $cache_dir_category); // category for cache
if(!$categories){
    $sql = "SELECT id, title, alias, section FROM category ORDER BY title ASC";
    $result = $connection->query($sql) or die(mysqli_error($connection));
    while ($rows = $result->fetch_assoc()) {
        $categories[$rows['section']][] = array('id' => $rows['id'], 'title' => $rows['title'], 'alias' => $rows['alias']);
    }
    create_cache($cache_key_category, $categories, $cache_dir_category); // create category cache;
} // ***************************** end category *****************************************


// #################              queries for index page           #####################
if($currentPage == 'index.php'){
    
    nukeMagicQuotes();
    foreach ($_POST as $key => $value){
        ${$key} = mysqli_real_escape_string($connection, trim($value));
    }

    $sqlLogin = "SELECT user_id, user_name, full_name, user_group, password FROM user WHERE user_name = '$username' AND password = '" . sha1($password) ."' AND user_status = 'Active'";
    $resultLogin = $connection->query($sqlLogin) or die (mysqli_error());
    $rowLogin = $resultLogin->fetch_assoc();
}

// #################              queries for home page           #####################
if($currentPage == 'home.php'){
    // count pending post
    if($_SESSION['userlevel'] != 'User'){
        // condition
        if($_SESSION['userlevel'] == 'Publisher' || $_SESSION['userlevel'] == 'Super Users'){
            $condition = "news_status = 'submitted to review' OR news_status = 'submitted to publish' OR news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Editor (publishing)'){
            $condition = "news_status = 'submitted to publish' OR news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Editor'){
            $condition = "news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Power Users'){
            $condition = "news_status = 'submitted to cook'";
        }
        
        // query
        $sqlCountPost = "SELECT news_id FROM news WHERE $condition";
        $resultCountPost = $connection->query($sqlCountPost) or die (mysqli_error());
        $totalPendingPost = mysqli_num_rows($resultCountPost);
    }
    
    $sqlArticle = "SELECT
        news_id,
        head_line,
        front_page,
        news_status,
        news_order,
        read_num,
        news.created AS created,
        news.publication_date AS published,
        publicationdate.publication_date AS printed,
        cat_display_name,
        section.title AS section_title,
        category.title AS category_title
        FROM news
        LEFT JOIN page_names ON news.news_cat_id = page_names.cat_id
        LEFT JOIN publicationdate ON news.news_pd_id = publicationdate.pd_id
        LEFT JOIN section ON news.section_id = section.id
        LEFT JOIN category ON news.category_id = category.id
        WHERE news.user_id = {$_SESSION['userid']} ORDER BY news_id DESC LIMIT 10";
    $resultArtilce = $connection->query($sqlArticle) or die ('ERROR: ' . mysqli_error($connection) . '. And QUERY: ' . $sqlArticle);
}

// #################              queries for issueNew page           #####################
if($currentPage == 'issueNew.php'){
    if(array_key_exists('submit', $_POST)){

        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            if($key == 'publicationdate'){
                ${$key} = mysqli_real_escape_string($connectionWrite, trim(date('Y-m-d', strtotime($value))));
            } else {
                ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
            }
        }
        
        $sql = "INSERT INTO publicationdate (publication_date, publication_des, status, created, user_id) 
                VALUES('$publicationdate', '$publicationdes', '$status', '" . date('Y-m-d H:i:s') . "', " . $_SESSION['userid'] .")";
        $result = $connectionWrite->query($sql);
        
        if($result){
            // create cache
            if($status == 'yes'){
                $sql = "SELECT * FROM publicationdate WHERE publication_date = '" . $publicationdate . "'";
                $result = $connection->query($sql) or die(mysqli_error($connection));
                $num_rows = $result->num_rows;
                if($num_rows > 0){
                    $issue = $result->fetch_assoc();
                    create_cache($publicationdate, $issue, $cache_dir_issue); // create issue cache;
                }

                $sqlMaxDate = "SELECT pd_id, publication_date, publication_des FROM publicationdate WHERE status = 'yes' ORDER BY publication_date DESC LIMIT 1";
                $resultMaxDate = $connection->query($sqlMaxDate) or die(mysqli_error);
                $rowMaxDate = $resultMaxDate->fetch_assoc();
                $num_rows = $resultMaxDate->num_rows;
                if($num_rows > 0){
                    create_cache('max_publication_date', $rowMaxDate, $cache_dir_issue); // create max publication date cache;
                }

                // rss cache
                $sqlRss = "SELECT news.news_id AS news_id, head_line, news_details, photo_name, created FROM publicationdate, news LEFT JOIN photos ON news.news_id = photos.news_id WHERE publicationdate.pd_id = news.news_pd_id AND front_page = 'yes' AND news_status = 'published' AND publicationdate.publication_date = '$publication_date_max' ORDER BY news_order ASC";
                $resultRss = $connection->query($sqlRss) or die(mysqli_error());
                if($resultRss->num_rows > 0){
                    while($rows = $resultRss->fetch_assoc()){$posts[] = $rows;}
                    create_cache($cache_key_rss, $posts, $cache_dir_rss); // create cache;
                }
            }
            $_SESSION['message'] = '<div class="alert alert-success" role="alert"><strong>Well done!</strong> new issue with the date <strong>'. $publicationdate .' </strong>has been created successfully.</div>';
            header("Location: ./issueManager.php");
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> new issue hasn’t been created. Please try <a href="./issueNew.php" class="alert-link">again</a>.</div>';
            header("Location: ./issueManager.php");
        }
    }
}

// #################              queries for issueManager page           #####################
if($currentPage == 'issueManager.php'){
    $sqlPublication = "SELECT * FROM publicationdate ORDER BY publication_date DESC LIMIT 15";
    $resultPublication = $connection->query($sqlPublication) or die (mysqli_error());
}

// #################              queries for issueEdit page           #####################
if($currentPage == 'issueEdit.php'){
    
    $pdid = (int)$_GET['pd_id'];
    
    if(array_key_exists('submit', $_POST)){
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            if($key == 'publicationdate'){
                ${$key} = mysqli_real_escape_string($connectionWrite, trim(date('Y-m-d', strtotime($value))));
            } else {
                ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
            }
        }

        $sqlIssueUpdate = "UPDATE publicationdate 
              SET publication_date = '$publicationdate', 
              publication_des = '$publicationdes', 
              status = '$status', 
              edited = '" . date('Y-m-d H:i:s') . "'
              WHERE pd_id = $pdid";
        $resultIssueUpdate = $connectionWrite->query($sqlIssueUpdate) or die('SQL: ' . $sqlIssueUpdate . '. and ERROR: ' . mysqli_error($connectionWrite));

        if($resultIssueUpdate){

            // issue cache
            $sql = "SELECT * FROM publicationdate WHERE publication_date = '" . $publicationdate . "'";
            $result = $connection->query($sql) or die(mysqli_error($connection));
            $num_rows = $result->num_rows;
            if($num_rows > 0){
                $issue = $result->fetch_assoc();
                create_cache($publicationdate, $issue, $cache_dir_issue); // create issue cache;
            }

            // last/max issue cache
            $sqlMaxDate = "SELECT pd_id, publication_date, publication_des FROM publicationdate WHERE status = 'yes' ORDER BY publication_date DESC LIMIT 1";
            $resultMaxDate = $connection->query($sqlMaxDate) or die(mysqli_error($connection));
            $rowMaxDate = $resultMaxDate->fetch_assoc();
            $num_rows = $resultMaxDate->num_rows;
            if($num_rows > 0){
                create_cache('max_publication_date', $rowMaxDate, $cache_dir_issue); // create max publication date cache;
            }

            // daily pages cache
            $sql = "SELECT DISTINCT page_names.cat_id AS id, cat_display_name AS title, alias "
                .   "FROM news, page_names "
                .   "WHERE page_names.cat_id = news.news_cat_id "
                .   "AND news_pd_id = '" .  $pdid . "' "
                .   "ORDER BY cat_order ASC";
            $result = $connection->query($sql) or die('SQL: ' . $sql . '. and ERROR: ' . mysqli_error($connection));
            $num_rows = $result->num_rows;
            if($num_rows > 0){
                $cache_key_daily_page = $publicationdate;
                $pages = array();
                while($row = $result->fetch_assoc()){
                    $pages[$row['id']] = array('title' => $row['title'], 'alias' => $row['alias']);
                }
                create_cache($cache_key_daily_page, $pages, $cache_dir_daily_page); // create daily_pages cache;
            }

            // rss cache
            $sqlRss = "SELECT news.news_id AS news_id, head_line, news_details, photo_name, news.created FROM publicationdate, news LEFT JOIN photos ON news.news_id = photos.news_id WHERE publicationdate.pd_id = news.news_pd_id AND front_page = 'yes' AND news_status = 'published' AND publicationdate.publication_date = '$publication_date_max' ORDER BY news_order ASC";
            $resultRss = $connection->query($sqlRss) or die(mysqli_error($connection));
            if($resultRss->num_rows > 0){
                while($rows = $resultRss->fetch_assoc()){$posts[] = $rows;}
                create_cache($cache_key_rss, $posts, $cache_dir_rss); // create cache;
            }

            $_SESSION['message'] = '<div class="alert alert-success" role="alert"><strong>Well done!</strong> issue with the date <strong>'. $publicationdate .' </strong>has been saved successfully.</div>';
            header("Location: ./issueManager.php");
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> the issue hasn’t been saved. Please try again.</div>';
            header("Location: ./issueManager.php");
        }
    }
}

// #################              queries for sectionNew page           #####################
if($currentPage == 'sectionNew.php'){
    $sqlSectionOrder = "SELECT sec_order FROM page_section ORDER BY sec_order DESC LIMIT 1";
    $resultSectionOrder = $connection->query($sqlSectionOrder);
    $row = $resultSectionOrder->fetch_assoc();
    
    if(array_key_exists('save', $_POST) || array_key_exists('savenew', $_POST)){
        if(array_key_exists('savenew', $_POST)){
            $saveNew = true;
        }
        
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        $sql = "INSERT INTO page_section (sec_name, sec_display_name, sec_order) VALUES('$sectionname', '$sectiondisplayname', '$sectionorder')";
        $result = $connectionWrite->query($sql);
        
        if($result && isset ($saveNew)){
            $insertResult = "<strong>$sectiondisplayname </strong>has been saved.";
            header("Location: ./sectionNew.php?result=$insertResult");
        } elseif($result && !isset ($saveNew)){
            $insertResult = "<strong>$sectiondisplayname </strong>has been saved.";
            header("Location: ./sectionManager.php?result=$insertResult");
        }
    }
    if(array_key_exists('cancel', $_POST)){
        header("Location: ./sectionManager.php");
    }
}

// #################              queries for sectionManager page           #####################
if($currentPage == 'sectionManager.php'){
    $sqlSection = "SELECT sec_id, sec_name, sec_display_name, sec_order FROM page_section ORDER BY sec_name ASC";
    $resultSection = $connection->query($sqlSection) or die (mysqli_error());
}

// #################              queries for sectionEdit page           #####################
if($currentPage == 'sectionEdit.php'){
    if(array_key_exists('save', $_POST)){
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        $sqlSectionUpdate = "UPDATE page_section SET sec_name = '$sectionname', sec_display_name = '$sectiondisplayname', sec_order = '$sectionorder' WHERE sec_id = $sectionid";
        $resultSectionUpdate = $connectionWrite->query($sqlSectionUpdate);
        
        if($resultSectionUpdate){
            header("Location: ./sectionManager.php?result=<strong>$sectiondisplayname </strong>has been updated");
        }
    } elseif(array_key_exists('cancel', $_POST)) {
        header("Location: ./sectionManager.php");
    }
}

// #################              queries for relatedTopicsNew page           #####################
if($currentPage == 'relatedTopicsNew.php'){
    if(array_key_exists('save', $_POST) || array_key_exists('savenew', $_POST)){
        if(array_key_exists('savenew', $_POST)){
            $saveNew = true;
        }
        
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        $sql = "INSERT INTO related_toptis (topits) VALUES('$topicsname')";
        $result = $connectionWrite->query($sql);
        
        if($result && isset ($saveNew)){
            $insertResult = "<strong> $topicsname </strong>has been saved.";
            header("Location: ./relatedTopicsNew.php?result=$insertResult");
        } elseif($result && !isset ($saveNew)){
            $insertResult = "<strong>$topicsname </strong>has been saved.";
            header("Location: ./relatedTopicsManager.php?result=$insertResult");
        }
        mysqli_close($connectionWrite);
    }
    if(array_key_exists('cancel', $_POST)){
        header("Location: ./relatedTopicsManager.php");
    }
}

// #################              queries for relatedTopicsManager page           #####################
if($currentPage == 'relatedTopicsManager.php'){
    $sqlRelatedTopics = "SELECT topits_id, topits FROM related_toptis ORDER BY topits_id ASC";
    $resultRelatedTopics = $connection->query($sqlRelatedTopics) or die (mysqli_error());
}

// #################              queries for relatedTopicsEdit page           #####################
if($currentPage == 'relatedTopicsEdit.php'){
    if(array_key_exists('save', $_POST)){
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        $sqlRelatedTopicsUpdate = "UPDATE related_toptis SET topits = '$topicsname' WHERE topits_id = $topicsid";
        $resultRelatedTopicsUpdate = $connectionWrite->query($sqlRelatedTopicsUpdate);
        
        if($resultRelatedTopicsUpdate){
            header("Location: ./relatedTopicsManager.php?result=<strong>$topicsname </strong>has been updated");
        }
    } elseif(array_key_exists('cancel', $_POST)) {
        header("Location: ./relatedTopicsManager.php");
    }
}

// #################              queries for categoryNew page           #####################
if($currentPage == 'categoryNew.php'){
    $sqlSection = "SELECT sec_id, sec_display_name FROM page_section";
    $resultSection = $connection->query($sqlSection) or die(mysqli_error());
    
    $sqlCategoryLast = "SELECT cat_order, cat_sec_id FROM page_names ORDER BY cat_id DESC LIMIT 1";
    $resultCategoryLast = $connection->query($sqlCategoryLast);
    $row = $resultCategoryLast->fetch_assoc();
    
    if(array_key_exists('save', $_POST) || array_key_exists('savenew', $_POST)){
        if(array_key_exists('savenew', $_POST)){
            $saveNew = true;
        }
        
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        $sql = "INSERT INTO page_names (cat_name, cat_display_name, cat_order, cat_sec_id) VALUES('$categoryname', '$categorydisplayname', '$categoryorder', '$section')";
        $result = $connectionWrite->query($sql);
        
        if($result && isset ($saveNew)){
            $insertResult = "$categorydisplayname has been saved.";
            header("Location: ./categoryNew.php?result=$insertResult");
        } elseif($result && !isset ($saveNew)){
            $insertResult = "$categorydisplayname has been saved.";
            header("Location: ./categoryManager.php?result=$insertResult");
        }
    }
    if(array_key_exists('cancel', $_POST)){
        header("Location: ./categoryManager.php");
    }
}

// #################              queries for categoryManager page           #####################
if($currentPage == 'categoryManager.php'){
    $sqlCategory = "SELECT cat_id, cat_name, cat_display_name, cat_order, sec_id, sec_display_name FROM page_names, page_section WHERE page_names.cat_sec_id = page_section.sec_id ORDER BY sec_name, cat_order ASC";
    $resultCategory = $connection->query($sqlCategory) or die (mysqli_error());
}

// #################              queries for categoryEdit page           #####################
if($currentPage == 'categoryEdit.php'){
    $sqlSection = "SELECT sec_id, sec_display_name FROM page_section";
    $resultSection = $connection->query($sqlSection) or die(mysqli_error());
    
    if(array_key_exists('save', $_POST)){
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        $sqlCategoryUpdate = "UPDATE page_names SET cat_name = '$categoryname', cat_display_name = '$categorydisplayname', cat_order = '$categoryorder', cat_sec_id = '$section' WHERE cat_id = $categoryid";
        $resultCategoryUpdate = $connectionWrite->query($sqlCategoryUpdate);
        
        if($resultCategoryUpdate){
            header("Location: ./categoryManager.php?result=$categorydisplayname has been updated");
        }
    } elseif(array_key_exists('cancel', $_POST)) {
        header("Location: ./categoryManager.php");
    }
}

// #################              queries for articleNew page           #####################
if($currentPage == 'articleNew.php'){
    //last data used by user
    $last_issue = check_last_value('last_issue', 'publicationdate');
    $last_category = check_last_value('last_category', 'page_name');
    $last_frontpage_status = check_last_value('last_frontpage_status', 'frontpage');
    $last_order_number = check_last_value('last_order_number', 'newsorder') + 1;

    // exit();
    // query for publication date
    $sqlDate = "SELECT pd_id, publication_date FROM publicationdate ORDER BY publication_date DESC LIMIT 15";
    $resultDate = $connection->query($sqlDate) or die(mysqli_error());

    // query for block
    $sql_block = "SELECT
        id,
        name
        FROM content_blocks
        ORDER BY id ASC";
    $result_block = $connection->query($sql_block) or die(mysqli_error($connection));

    // query for category
    $sqlCategory = "SELECT
        cat_id,
        cat_display_name,
        sec_display_name
        FROM page_names, page_section
        WHERE page_names.cat_sec_id = page_section.sec_id
        ORDER BY sec_name, cat_order ASC";
    $resultCategory = $connection->query($sqlCategory) or die(mysqli_error());
    
    // query for subject
    // $sqlSebject = "SELECT topits_id, topits FROM related_toptis ORDER BY topits ASC";
    // $resultSubject = $connection->query($sqlSebject) or die(mysqli_error());

    if(array_key_exists('submit', $_POST)){
        // redirect location
        if($_POST['submit'] == 'saved as draft' || 
        $_POST['submit'] == 'published' ||
        $_POST['submit'] == 'submitted to review' ||
        $_POST['submit'] == 'submitted to publish' ||
        $_POST['submit'] == 'submitted to edit' ||
        $_POST['submit'] == 'submitted to cook'){
            $redirect_location = './my_posts.php';
        } else {
            $redirect_location = './articleNew.php';
        }

		if($_POST['publicationdate']){
			while($rows = $resultDate->fetch_assoc()){
				if($_POST['publicationdate'] == $rows['pd_id']){
					$issue_date = $rows['publication_date'];
				}
			}
		} else {
			$issue_date = 'NULL';
		}
		
		
        // upload intro media
        if(!empty($_FILES['intro_media'])){
            $destination = '../images/';
            require_once 'classes/uploadFile.class.php';
            try {
                $upload = new UploadFile($destination);
                $upload->addPermittedTypes('text/plain');
                $upload->setMaxSize(92160);
                $upload->move();
                $_POST['intro_img_name'] = $upload->getFilename();
                $_SESSION['message'] = $upload->getMessages();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            if($key == 'submit'){
                $value = ($value == 'published & new' || $value == 'saved as draft & new' || $value == 'submitted to edit & new' || $value == 'submitted to publish & new' || $value == 'submitted to review & new') ? str_replace("& new", "", "$value") : $value;
                ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
            } elseif($key == 'start'){
                ${$key} = $value ? date('Y-m-d H:i:s', strtotime($value)) : ($submit == 'published' ? date('Y-m-d H:i:s') : NULL);
            } else {
                ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
            }
        }

        // media caption & file
        $media = array('intro_img_name' => '', 'intro_img_caption' => '');
        if(!empty($_POST['intro_img_name'][0])) $media['intro_img_name'] = $_POST['intro_img_name'][0];
        if(!empty($intro_media_caption)) $media['intro_img_caption'] = $intro_media_caption;
        $media = serialize($media);

        
        $sqlArticleInsert = "
          INSERT INTO news (top_shoulder, head_line, news_cat_id, news_pd_id, issue_date, publication_date, news_details, media, front_page, news_status, created, news_order, user_id, section_id, category_id, block_id)
          VALUES('$topsubhead', '$headline', '$page_name', '$publicationdate', '$issue_date', '$start', '$articledetails', '$media', '$frontpage', '$submit', '" . date('Y-m-d H:i:s') . "', $newsorder, {$_SESSION['userid']}, '$section', '$category', '$block')";
        $resultArticleInsert = $connectionWrite->query($sqlArticleInsert);
        
        if($resultArticleInsert){
            $_SESSION['message'] = '<div class="alert alert-success" role="alert"><strong>Well done!</strong> your post <strong>'. $headline .' </strong>has been saved successfully.</div>';
			
			if($submit == 'published') {
				// latest_news cache
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
				$result_last_update = $connection->query($sql_last_update) or die(mysqli_error($connection));
				// create cache
				while($rows = $result_last_update->fetch_assoc()){$latest_news[] = $rows;}
				create_cache($cache_key_latest_news, $latest_news, $cache_dir_latest_news); // create cache;
				
				// Home Lead cache
				if($block == 1 && $frontpage == 'yes'){
					$sqlHomeLead = "
						SELECT
						news.news_id AS news_id,
						head_line,
						top_shoulder,
						news_details,
						media
						FROM news
						WHERE news_status = 'published'
						AND front_page = 'yes'
						AND block_id = 1
						ORDER BY news.news_id DESC
						LIMIT 1";
					$resultHomeLead = $connection->query($sqlHomeLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultHomeLead->fetch_assoc()){$home_lead[] = $rows;}
					create_cache('home_lead', $home_lead, $cache_dir_home); // create cache;
				}
				
				// second lead cache
				if($block == 2 && $frontpage == 'yes'){
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
					$resultSecondLead = $connection->query($sqlSecondLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultSecondLead->fetch_assoc()){$second_lead[] = $rows;}
					create_cache('second_lead', $second_lead, $cache_dir_home); // create cache;
				}
				
				// Feature lead
				if($block == 3 && $frontpage == 'yes'){
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
					$resultFeatureLead = $connection->query($sqlFeatureLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultFeatureLead->fetch_assoc()){$feature_lead[] = $rows;}
					create_cache('feature_lead', $feature_lead, $cache_dir_home); // create cache;
				}
				
				// Bangladesh lead
				if($block == 4 && $frontpage == 'yes' && $section == 1){
					$sqlBangladeshLead = "
						SELECT
						news.news_id AS news_id,
						head_line,
						top_shoulder,
						news_details,
						media
						FROM news
						WHERE news_status = 'published'
						AND front_page = 'yes'
						AND section_id = 1
						AND block_id = 4
						ORDER BY news.news_id DESC
						LIMIT 1";
					$resultBangladeshLead = $connection->query($sqlBangladeshLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultBangladeshLead->fetch_assoc()){$bangladesh_lead[] = $rows;}
					create_cache('bangladesh_lead', $bangladesh_lead, $cache_dir_home); // create cache;
				}
				
				// Bangladesh others
				if($block == 0 && $frontpage == 'yes' && $section == 1){
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
					$resultBangladeshOthers = $connection->query($sqlBangladeshOthers) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultBangladeshOthers->fetch_assoc()){$bangladesh_others[] = $rows;}
					create_cache('bangladesh_others', $bangladesh_others, $cache_dir_home); // create cache;
				}
				
				// International
				if($block == 0 && $frontpage == 'yes' && $section == 2){
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
					$resultInternational = $connection->query($sqlInternational) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultInternational->fetch_assoc()){$international[] = $rows;}
					create_cache('international', $international, $cache_dir_home); // create cache;
				}
				
				// sports
				if($block == 0 && $frontpage == 'yes' && $section == 3){
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
					$resultSports = $connection->query($sqlSports) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultSports->fetch_assoc()){$sports[] = $rows;}
					create_cache('sports', $sports, $cache_dir_home); // create cache;
				}
				
				// Life style lead
				if($block == 4 && $frontpage == 'yes' && $section == 11){
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
					$resultLifeStyleLead = $connection->query($sqlLifeStyleLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultLifeStyleLead->fetch_assoc()){$life_style_lead[] = $rows;}
					create_cache('life_style_lead', $life_style_lead, $cache_dir_home); // create cache;
				}
				
				// Life style others
				if($block == 0 && $frontpage == 'yes' && $section == 11){
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
					$resultLifeStyleOthers = $connection->query($sqlLifeStyleOthers) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultLifeStyleOthers->fetch_assoc()){$life_style_others[] = $rows;}
					create_cache('life_style_others', $life_style_others, $cache_dir_home); // create cache;
				}
				
				// feature
				if($block == 0 && $frontpage == 'yes' && $section == 9){
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
					create_cache('feature', $feature, $cache_dir_home); // create cache;
				}
				
				// science technology
				if($block == 0 && $frontpage == 'yes' && $section == 10){
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
					$resultScienceTechnology = $connection->query($sqlScienceTechnology) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultScienceTechnology->fetch_assoc()){$science_technology[] = $rows;}
					create_cache('science_technology', $science_technology, $cache_dir_home); // create cache;
				}
				
				// opinion
				if($block == 0 && $frontpage == 'yes' && $section == 6){
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
					$resultOpinion = $connection->query($sqlOpinion) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultOpinion->fetch_assoc()){$opinion[] = $rows;}
					create_cache('opinion', $opinion, $cache_dir_home); // create cache;
				}

                // erase section or category cache
                $section_category = false;
                $section_category_count = false;
                foreach($categories[$section] as $cat){
                    if($cat['id'] == $category){
                        $section_category = $sections[$section]["alias"] . '_' . $cat['alias'];
                        $section_category_count = $section_category . '_count';
                    }
                }

                if($section_category){
                    $cache = new Cache();
                    $cache->setCachePath($cache_dir_section_page);
                    if(in_array($section_category, array_keys($cache->retrieveAll()))){
                        var_dump($cache->erase($section_category));
                    }
                    if(in_array($section_category_count, array_keys($cache->retrieveAll()))){
                        var_dump($cache->erase($section_category_count));
                    }
                } // end erase section or category cache
			}
			
            header("Location: $redirect_location");
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> your post <strong>'. $headline .' </strong>hasn’t been saved. Please try <a href="./articleNew.php" class="alert-link">again</a>.</div>';
            header("Location: ./articleManager.php");
        }
    }
}

// #################              queries for my_posts page           #####################
if($currentPage == 'my_posts.php'){
    // where i am stating
    $_SESSION['previous_page'] = 'my_posts.php';

    // count pending post
    if($_SESSION['userlevel'] != 'User'){
        // condition
        if($_SESSION['userlevel'] == 'Publisher' || $_SESSION['userlevel'] == 'Super Users'){
            $condition = "news_status = 'submitted to review' OR news_status = 'submitted to publish' OR news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Editor (publishing)'){
            $condition = "news_status = 'submitted to publish' OR news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Editor'){
            $condition = "news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Power Users'){
            $condition = "news_status = 'submitted to cook'";
        }
        
        // query
        $sqlCountPost = "SELECT news_id FROM news WHERE $condition";
        $resultCountPost = $connection->query($sqlCountPost) or die (mysqli_error());
        $totalPendingPost = mysqli_num_rows($resultCountPost);
    }

    // query for page
    $sqlPage = "SELECT cat_id, cat_display_name, sec_display_name FROM page_names, page_section WHERE page_names.cat_sec_id = page_section.sec_id ORDER BY sec_name, cat_order ASC";
    $resultPage = $connection->query($sqlPage) or die(mysqli_error());
    
    //post search
    if(array_key_exists('page', $_POST)){
        $_SESSION['my_post_page'] = $_POST['page'];
    }
    
    if(array_key_exists('reset', $_POST)){
        $_SESSION['my_post_page'] = NULL;
    }
    
    // pagination
    $sqlCountPost = isset($_SESSION['my_post_page']) ? 
                    "SELECT news_id FROM news WHERE news_cat_id = {$_SESSION['my_post_page']} AND user_id = {$_SESSION['userid']}" : 
                    "SELECT news_id FROM news WHERE user_id = {$_SESSION['userid']}";
    $resultCountPost = $connection->query($sqlCountPost) or die (mysqli_error($connection));
    $totalPost = mysqli_num_rows($resultCountPost);

    // pagination config
    $postPerPage = 12;
    $pagenumber = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 0;
    $totalrecords = $totalPost;
    $limit = (isset($_GET['page']) && is_numeric($_GET['page']) && $pagenumber) ? " LIMIT ".(($pagenumber-1)*$postPerPage).",$postPerPage" : " LIMIT $postPerPage";

    include_once("./classes/pagination.php"); 
    $pg = new bootPagination();
    $pg->pagenumber = $pagenumber;
    $pg->totalrecords = $totalrecords;
    $pg->showfirst = false;
    $pg->showlast = false;
    $pg->paginationcss = "pagination-sm pull-right";
    $pg->paginationstyle = 0; // 1: advance, 0: normal
    $pg->defaultUrl = "my_posts.php";
    $pg->paginationUrl = "my_posts.php?page=[p]";
    
    $sqlArticle = isset($_SESSION['my_post_page']) ? 
    "SELECT 
    news_id, 
    head_line, 
    front_page, 
    news_status, 
    news_order, 
    read_num, 
    news.publication_date AS published,
    publicationdate.publication_date AS printed,
    cat_display_name,
    section.title AS section_title,
    category.title AS category_title
    FROM news
    LEFT JOIN page_names ON news.news_cat_id = page_names.cat_id 
    LEFT JOIN publicationdate ON news.news_pd_id = publicationdate.pd_id
    LEFT JOIN section ON news.section_id = section.id
    LEFT JOIN category ON news.category_id = category.id
    WHERE news_cat_id = {$_SESSION['my_post_page']}
    AND news.user_id = {$_SESSION['userid']} ORDER BY news_id DESC $limit" :
    "SELECT 
    news_id, 
    head_line, 
    front_page, 
    news_status, 
    news_order, 
    read_num, 
    news.publication_date AS published,
    publicationdate.publication_date AS printed,
    cat_display_name,
    section.title AS section_title,
    category.title AS category_title
    FROM news
    LEFT JOIN page_names ON news.news_cat_id = page_names.cat_id
    LEFT JOIN publicationdate ON news.news_pd_id = publicationdate.pd_id
    LEFT JOIN section ON news.section_id = section.id
    LEFT JOIN category ON news.category_id = category.id
    WHERE news.user_id = {$_SESSION['userid']} ORDER BY news_id DESC $limit";
    $resultArtilce = $connection->query($sqlArticle) or die (mysqli_error($connection));
}

// #################              queries for my_posts page           #####################
if($currentPage == 'pending_posts.php'){
    // where i am stating
    $_SESSION['previous_page'] = 'pending_posts.php';

    $power_users = array('submitted to cook');
    $editor = array_merge(array('submitted to edit'), $power_users);
    $editor_publishing = array_merge(array('submitted to publish'), $editor);
    $publisher = array_merge(array('submitted to review'), $editor_publishing);

    if($_SESSION['userlevel'] == 'Power Users') $status_condition = implode("', '", $power_users);
    elseif($_SESSION['userlevel'] == 'Editor') $status_condition = implode("', '", $editor);
    elseif($_SESSION['userlevel'] == 'Editor (publishing)') $status_condition = implode("', '", $editor_publishing);
    elseif($_SESSION['userlevel'] == 'Publisher' || $_SESSION['userlevel'] == 'Super Users') $status_condition = implode("', '", $publisher);

    $sqlArticle = "SELECT 
    news_id, 
    head_line, 
    news_status, 
    news.created, 
    cat_display_name,
    section.title AS section_title,
    category.title AS category_title,
    full_name
    FROM user, news
    LEFT JOIN page_names ON news.news_cat_id = page_names.cat_id
    LEFT JOIN section ON news.section_id = section.id
    LEFT JOIN category ON news.category_id = category.id
    WHERE news.user_id = user.user_id
    AND news_status IN ('$status_condition')
    ORDER BY news_id DESC LIMIT 20";
    $resultArtilce = $connection->query($sqlArticle) or die (mysqli_error($connection));
}

// #################              queries for articleManager page           #####################
if($currentPage == 'articleManager.php'){
    // where i am stating
    $_SESSION['previous_page'] = 'articleManager.php';

    // count pending post
    if($_SESSION['userlevel'] != 'User'){
        // condition
        if($_SESSION['userlevel'] == 'Publisher' || $_SESSION['userlevel'] == 'Super Users'){
            $condition = "news_status = 'submitted to review' OR news_status = 'submitted to publish' OR news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Editor (publishing)'){
            $condition = "news_status = 'submitted to publish' OR news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Editor'){
            $condition = "news_status = 'submitted to edit' OR news_status = 'submitted to cook'";
        }elseif($_SESSION['userlevel'] == 'Power Users'){
            $condition = "news_status = 'submitted to cook'";
        }
        
        // query
        $sqlCountPendingPost = "SELECT news_id FROM news WHERE $condition";
        $resultCountPendingPost = $connection->query($sqlCountPendingPost) or die (mysqli_error());
        $totalPendingPost = mysqli_num_rows($resultCountPendingPost);
    }
    
    // query for page
    $sqlPage = "SELECT cat_id, cat_display_name, sec_display_name FROM page_names, page_section WHERE page_names.cat_sec_id = page_section.sec_id ORDER BY sec_name, cat_order ASC";
    $resultPage = $connection->query($sqlPage) or die(mysqli_error());
    
    //post search
    if(array_key_exists('page', $_POST)){
        $_SESSION['page'] = $_POST['page'];
    }
    
    if(array_key_exists('reset', $_POST)){
        $_SESSION['page'] = NULL;
    }
    
    // pagination
    $sqlCountPost = isset($_SESSION['page']) ? 
                    "SELECT news_id FROM news WHERE news_cat_id = {$_SESSION['page']} AND (news_status = 'published' OR news_status = 'withdrawn')" : 
                    "SELECT news_id FROM news WHERE (news_status = 'published' OR news_status = 'withdrawn')";
    $resultCountPost = $connection->query($sqlCountPost) or die (mysqli_error());
    $totalPost = mysqli_num_rows($resultCountPost);

    // pagination config
    $postPerPage = 12;
    $pagenumber = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 0;
    $totalrecords = $totalPost;
    
    $limit = (isset($_GET['page']) && is_numeric($_GET['page']) && $pagenumber) ? " LIMIT ".(($pagenumber-1)*$postPerPage).",$postPerPage" : " LIMIT $postPerPage";

    include_once("./classes/pagination.php"); 
    $pg = new bootPagination();
    $pg->pagenumber = $pagenumber;
    $pg->totalrecords = $totalrecords;
    $pg->showfirst = false;
    $pg->showlast = false;
    $pg->paginationcss = "pagination-sm pull-right";
    $pg->paginationstyle = 0; // 1: advance, 0: normal
    $pg->defaultUrl = "articleManager.php";
    $pg->paginationUrl = "articleManager.php?page=[p]";
    
    // post query
    $sqlArticle = isset($_SESSION['page']) ? 
    "SELECT 
    news_id, 
    head_line, 
    front_page, 
    news_status, 
    news_order, 
    read_num, 
    news.publication_date AS published,
    publicationdate.publication_date AS printed,
    cat_display_name,
    section.title AS section_title,
    category.title AS category_title,
    full_name
    FROM news
    LEFT JOIN page_names ON news.news_cat_id = page_names.cat_id
    LEFT JOIN publicationdate ON news.news_pd_id = publicationdate.pd_id
    LEFT JOIN section ON news.section_id = section.id
    LEFT JOIN category ON news.category_id = category.id
    LEFT JOIN user ON news.user_id = user.user_id
    WHERE news_cat_id = {$_SESSION['page']}
    AND (news_status = 'published' OR news_status = 'withdrawn')
    ORDER BY news_id DESC $limit" : 
    "SELECT 
    news_id, 
    head_line, 
    front_page, 
    news_status, 
    news_order, 
    read_num, 
    news.publication_date AS published,
    publicationdate.publication_date AS printed,
    cat_display_name,
    section.title AS section_title,
    category.title AS category_title,
    full_name
    FROM news
    LEFT JOIN page_names ON news.news_cat_id = page_names.cat_id
    LEFT JOIN publicationdate ON news.news_pd_id = publicationdate.pd_id
    LEFT JOIN section ON news.section_id = section.id
    LEFT JOIN category ON news.category_id = category.id
    LEFT JOIN user ON news.user_id = user.user_id
    WHERE (news_status = 'published' OR news_status = 'withdrawn')
    ORDER BY news_id DESC $limit";
    $resultArtilce = $connection->query($sqlArticle) or die (mysqli_error());
}

// #################              queries for articleEdit page           #####################
if($currentPage == 'articleEdit.php'){
    if(isset ($_GET['news_id']) && is_numeric($_GET['news_id'])){
        $newsId = (int)$_GET['news_id'];
		
		$cache_key_post = $newsId;
		$post = get_cache($cache_key_post, $cache_dir_post); // get post from cache
        
        // query for publication date
        $sqlDate = "SELECT pd_id, publication_date FROM publicationdate ORDER BY publication_date DESC";
        $resultDate = $connection->query($sqlDate) or die(mysqli_error());

        // query for page_names
        $sqlCategory = "SELECT cat_id, cat_display_name, sec_display_name FROM page_names, page_section WHERE page_names.cat_sec_id = page_section.sec_id ORDER BY sec_name, cat_order ASC";
        $resultCategory = $connection->query($sqlCategory) or die(mysqli_error());

        // query for block
        $sql_block = "SELECT
            id,
            name
            FROM content_blocks
            ORDER BY id ASC";
        $result_block = $connection->query($sql_block) or die(mysqli_error($connection));

        // query for article
		if(!$post){
			$sqlArticle = "SELECT 
				top_shoulder, 
				head_line, 
				news_cat_id,
				section_id,
				category_id,
				block_id,
				news_pd_id, 
				news_topits_id, 
				news_details,
				media,
				publication_date,
				front_page, 
				news_status, 
				news_order, 
				user_id, 
				created FROM news WHERE news_id = $newsId";
			$resultArtilce = $connection->query($sqlArticle) or die(mysqli_error());
			$row = $resultArtilce->fetch_assoc();
		} else {
			$row = $post;
		}
        
        // media
        if($row){
            $row['media'] = unserialize($row['media']);
        }
    } else {
        header("Location: ./articleManager.php");
    }
    
    if(array_key_exists('submit', $_POST)){
        
        // redirect location
        if($_POST['submit'] == 'save & new' || $_POST['submit'] == 'published & new'){
            $redirect_location = './articleNew.php';
        } elseif($_POST['submit'] == 'save & close') {
            $redirect_location = './' . $_SESSION['previous_page'];
        } elseif($_SESSION['userid'] == $row['user_id'] && (
                $_POST['submit'] == 'published' ||
                $_POST['submit'] == 'submitted to review' ||
                $_POST['submit'] == 'submitted to publish' ||
                $_POST['submit'] == 'submitted to edit' ||
                $_POST['submit'] == 'submitted to cook' ||
                $_POST['submit'] == 'rejected'
            )){
            $redirect_location = './my_posts.php';
        } else {
            $redirect_location = './' . $_SESSION['previous_page'];
        }
		
		if($_POST['publicationdate']){
			while($rows = $resultDate->fetch_assoc()){
				if($_POST['publicationdate'] == $rows['pd_id']){
					$issue_date = $rows['publication_date'];
				}
			}
		} else {
			$issue_date = 'NULL';
		}

        // upload intro media
        if(!empty($_FILES['intro_media'])){
            $destination = '../images/';
            require_once 'classes/uploadFile.class.php';
            try {
                $upload = new UploadFile($destination);
                $upload->addPermittedTypes('text/plain');
                $upload->setMaxSize(92160);
                $upload->move();
                $_POST['intro_img_name'] = $upload->getFilename();
                $_SESSION['message'] = $upload->getMessages();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            if($key == 'submit'){
                $value = $value == 'published & new' ? str_replace("& new", "", "$value") : $value;
                ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
            } elseif($key == 'start'){
                ${$key} = $value ? date('Y-m-d H:i:s', strtotime($value)) : ($submit == 'published' ? date('Y-m-d H:i:s') : NULL);
            } else {
                ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
            }
        }

        // media caption & file
        $media = array('intro_img_name' => '', 'intro_img_caption' => '');
        $media['intro_img_name'] = $_POST['intro_img_name'][0] ? $_POST['intro_img_name'][0] : $row['media']['intro_img_name'];
        $media['intro_img_caption'] = $intro_media_caption ? $intro_media_caption : $row['media']['intro_img_caption'];
        $media = serialize($media);

        // prepare query statement
        if($submit == 'saved' || $submit == 'save & close' || $submit == 'save & new'){
            $sqlArticleUpdate = "UPDATE news SET ".
            "head_line = '$headline', ".
            "top_shoulder = '$topsubhead', ".
            "news_cat_id = '$page_name', ".
            "section_id = '$section', ".
            "category_id = '$category', ".
            "block_id = '$block', ".
            "news_pd_id = '$publicationdate', ".
            "issue_date = '$issue_date', ".
            "publication_date = '$start', ".
            "news_details = '$articledetails', ".
            "media = '$media', ".
            "front_page = '$frontpage', ".
            "news_order = '$newsorder', ".
            "edited = '" . date('Y-m-d H:i:s') . "' ".
            "WHERE news_id = $newsId";
        } else {
            $sqlArticleUpdate = "UPDATE news SET ".
            "head_line = '$headline', ".
            "top_shoulder = '$topsubhead', ".
            "news_cat_id = '$page_name', ".
            "section_id = '$section', ".
            "category_id = '$category', ".
            "block_id = '$block', ".
            "news_pd_id = '$publicationdate', ".
			"issue_date = '$issue_date', ".
            "publication_date = '$start', ".
            "news_details = '$articledetails', ".
            "media = '$media', ".
            "front_page = '$frontpage', ".
            "news_status = '$submit', ".
            "news_order = '$newsorder', ".
            "edited = '" . date('Y-m-d H:i:s') . "' ".
            "WHERE news_id = $newsId";
        }

        $resultArticaleUpdate = $connectionWrite->query($sqlArticleUpdate) or die(mysqli_error());

        if($resultArticaleUpdate){
			
			// create cache
			$sql = "SELECT * FROM news WHERE news_id = $cache_key_post";
			$result = $connection->query($sql) or die(mysqli_error($connection));
			$post = $result->fetch_assoc();
                        var_dump($post);
                        exit();
			create_cache($cache_key_post, $post, $cache_dir_post); // create cache;
			
			if($submit == 'published' || $row['news_status'] == 'published'){
				// Home Lead cache
				if(($block == 1 && $frontpage == 'yes') || $row['block_id'] == 1){
					$sqlHomeLead = "
						SELECT
						news.news_id AS news_id,
						head_line,
						top_shoulder,
						news_details,
						media
						FROM news
						WHERE news_status = 'published'
						AND front_page = 'yes'
						AND block_id = 1
						ORDER BY news.news_id DESC
						LIMIT 1";
					$resultHomeLead = $connection->query($sqlHomeLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultHomeLead->fetch_assoc()){$home_lead[] = $rows;}
					create_cache('home_lead', $home_lead, $cache_dir_home); // create cache;
				}
				
				// second lead cache
				if(($block == 2 && $frontpage == 'yes') || $row['block_id'] == 2){
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
					$resultSecondLead = $connection->query($sqlSecondLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultSecondLead->fetch_assoc()){$second_lead[] = $rows;}
					create_cache('second_lead', $second_lead, $cache_dir_home); // create cache;
				}
				
				// Feature lead
				if(($block == 3 && $frontpage == 'yes') || $row['block_id'] == 3){
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
					$resultFeatureLead = $connection->query($sqlFeatureLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultFeatureLead->fetch_assoc()){$feature_lead[] = $rows;}
					create_cache('feature_lead', $feature_lead, $cache_dir_home); // create cache;
				}
				
				// Bangladesh lead
				if(($block == 4 && $frontpage == 'yes' && $section == 1) || ($row['block_id'] == 4 && $row['section_id'] == 1)){
					$sqlBangladeshLead = "
						SELECT
						news.news_id AS news_id,
						head_line,
						top_shoulder,
						news_details,
						media
						FROM news
						WHERE news_status = 'published'
						AND front_page = 'yes'
						AND section_id = 1
						AND block_id = 4
						ORDER BY news.news_id DESC
						LIMIT 1";
					$resultBangladeshLead = $connection->query($sqlBangladeshLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultBangladeshLead->fetch_assoc()){$bangladesh_lead[] = $rows;}
					create_cache('bangladesh_lead', $bangladesh_lead, $cache_dir_home); // create cache;
				}
				
				// Bangladesh others
				if(($block == 0 && $frontpage == 'yes' && $section == 1) || ($row['block_id'] == 0 && $row['section_id'] == 1)){
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
					$resultBangladeshOthers = $connection->query($sqlBangladeshOthers) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultBangladeshOthers->fetch_assoc()){$bangladesh_others[] = $rows;}
					create_cache('bangladesh_others', $bangladesh_others, $cache_dir_home); // create cache;
				}
				
				// International
				if(($block == 0 && $frontpage == 'yes' && $section == 2) || ($row['block_id'] == 0 && $row['section_id'] == 2)){
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
					$resultInternational = $connection->query($sqlInternational) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultInternational->fetch_assoc()){$international[] = $rows;}
					create_cache('international', $international, $cache_dir_home); // create cache;
				}
				
				// sports
				if(($block == 0 && $frontpage == 'yes' && $section == 3) || ($row['block_id'] == 0 && $row['section_id'] == 3)){
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
					$resultSports = $connection->query($sqlSports) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultSports->fetch_assoc()){$sports[] = $rows;}
					create_cache('sports', $sports, $cache_dir_home); // create cache;
				}
				
				// Life style lead
				if(($block == 4 && $frontpage == 'yes' && $section == 11) || ($row['block_id'] == 4 && $row['section_id'] == 11)){
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
					$resultLifeStyleLead = $connection->query($sqlLifeStyleLead) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultLifeStyleLead->fetch_assoc()){$life_style_lead[] = $rows;}
					create_cache('life_style_lead', $life_style_lead, $cache_dir_home); // create cache;
				}
				
				// Life style others
				if(($block == 0 && $frontpage == 'yes' && $section == 11) || ($row['block_id'] == 0 && $row['section_id'] == 11)){
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
					$resultLifeStyleOthers = $connection->query($sqlLifeStyleOthers) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultLifeStyleOthers->fetch_assoc()){$life_style_others[] = $rows;}
					create_cache('life_style_others', $life_style_others, $cache_dir_home); // create cache;
				}
				
				// feature
				if(($block == 0 && $frontpage == 'yes' && $section == 9) || ($row['block_id'] == 0 && $row['section_id'] == 9)){
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
					create_cache('feature', $feature, $cache_dir_home); // create cache;
				}
				
				// science technology
				if(($block == 0 && $frontpage == 'yes' && $section == 10) || ($row['block_id'] == 0 && $row['section_id'] == 10)){
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
					$resultScienceTechnology = $connection->query($sqlScienceTechnology) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultScienceTechnology->fetch_assoc()){$science_technology[] = $rows;}
					create_cache('science_technology', $science_technology, $cache_dir_home); // create cache;
				}
				
				// opinion
				if(($block == 0 && $frontpage == 'yes' && $section == 6) || ($row['block_id'] == 0 && $row['section_id'] == 6)){
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
					$resultOpinion = $connection->query($sqlOpinion) or die(mysqli_error($connection));
					// create cache
					while($rows = $resultOpinion->fetch_assoc()){$opinion[] = $rows;}
					create_cache('opinion', $opinion, $cache_dir_home); // create cache;
				}

                // erase section or category cache
                $section_category = false;
                $section_category_count = false;
                foreach($categories[$section] as $cat){
                    if($cat['id'] == $category){
                        $section_category = $sections[$section]["alias"] . '_' . $cat['alias'];
                        $section_category_count = $section_category . '_count';
                    }
                }

                if($section_category){
                    $cache = new Cache();
                    $cache->setCachePath($cache_dir_section_page);
                    if(in_array($section_category, array_keys($cache->retrieveAll()))){
                        var_dump($cache->erase($section_category));
                    }
                    if(in_array($section_category_count, array_keys($cache->retrieveAll()))){
                        var_dump($cache->erase($section_category_count));
                    }
                } // end erase section or category cache
			}

            $_SESSION['message'] = '<div class="alert alert-success" role="alert"><strong>Well done!</strong> your post <strong>'. $headline .' </strong>has been saved successfully.</div>';
            
            if($submit != 'saved'){
                header("Location: $redirect_location");
            } else {
                header("Location: ./articleEdit.php?news_id=$newsId");
            }
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> your post <strong>'. $headline .' </strong>hasn’t been saved. Please try <a href="./articleNew.php" class="alert-link">again</a>.</div>';
        }
    }
}

// #################              queries for imageManager page           #####################
if($currentPage == 'imageManager.php'){
    $sqlImage = "SELECT photo_id, photo_name, caption, head_line FROM news, photos WHERE news.news_id = photos.news_id ORDER BY photo_id DESC LIMIT 20";
    $resultImage = $connection->query($sqlImage) or die (mysqli_error());
}

// #################              queries for imageNew page           #####################
if($currentPage == 'imageNew.php'){
    $sqlLastDate = "SELECT pd_id, publication_date FROM publicationdate ORDER BY publication_date DESC LIMIT 1";
    $resultLastDate = $connection->query($sqlLastDate) or die (mysqli_error());
    $rowLastDate = $resultLastDate->fetch_assoc();
    $lastDate = $rowLastDate['pd_id'];
    
    $sqlHeadline = "SELECT news.news_id AS news_id, head_line, cat_display_name, photos.news_id AS photo_news_id FROM page_section, page_names, news LEFT JOIN photos ON news.news_id = photos.news_id WHERE news.news_cat_id = page_names.cat_id AND page_section.sec_id = page_names.cat_sec_id AND news_pd_id = $lastDate ORDER BY sec_id, cat_order, news.news_id ASC";
    $resultHeadline = $connection->query($sqlHeadline) or die (mysqli_error());
}

// #################              queries for commentManager page           #####################
//if($currentPage == 'commentManager.php'){
//    $sqlComments = "SELECT com_id, comments_details, com_status, com_edited, com_created, head_line, username FROM comments, commenter, news WHERE comments.com_comtid = commenter.comt_id AND comments.com_newsid = news.news_id ORDER BY com_id DESC";
//    $resultComments = $connection->query($sqlComments) or die (mysqli_error());
//}

// #################              queries for commentEdit page           #####################
//if($currentPage == 'commentEdit.php'){
//    if(isset ($_GET['com_id']) && is_numeric($_GET['com_id'])){
//        $comid = (int)$_GET['com_id'];
//        
//        $sqlComment = "SELECT com_id, comments_details, com_status, com_edited, com_created, head_line, username FROM comments, commenter, news WHERE comments.com_comtid = commenter.comt_id AND comments.com_newsid = news.news_id AND com_id = $comid";
//        $resultComment = $connection->query($sqlComment) or die (mysqli_error());
//        $row = $resultComment->fetch_assoc();
//    } else {
//        header("Location: ".SITEURL."/commentManager.php");
//    }
//    
//    if(array_key_exists('save', $_POST)){
//        nukeMagicQuotes();
//        $connectionWrite = dbConnect('write', MYDATABASE);
//        foreach ($_POST as $key => $value){
//            ${$key} = trim(mysqli_real_escape_string($connectionWrite, $value));
//        }
//        
//        $sqlCommentUpdate = "UPDATE comments SET comments_details = '$commentsdetails', com_status = '$status', com_edited = '$edited', com_created = '$created' WHERE com_id = $comid";
//        $resultCommentUpdate = $connectionWrite->query($sqlCommentUpdate);
//        
//        if($resultCommentUpdate){
//            header("Location: ".SITEURL."/commentManager.php?result=comment has been updated");
//        }
//    } elseif(array_key_exists('cancel', $_POST)) {
//        header("Location: ".SITEURL."/commentManager.php");
//    }
//}

// #################              queries for commenterManager page           #####################
//if($currentPage == 'commenterManager.php'){
//    $sqlCommenter = "SELECT * FROM commenter ORDER BY comt_id DESC";
//    $resultCommenter = $connection->query($sqlCommenter) or die (mysqli_error());
//}

// #################              queries for commenterEdit page           #####################
//if($currentPage == 'commenterEdit.php'){
//    if(isset ($_GET['comt_id']) && is_numeric($_GET['comt_id'])){
//        $comtid = (int)$_GET['comt_id'];
//        
//        $sqlCommenter = "SELECT * FROM commenter WHERE comt_id = $comtid";
//        $resultCommenter = $connection->query($sqlCommenter) or die (mysqli_error());
//        $row = $resultCommenter->fetch_assoc();
//    } else {
//        header("Location: ".SITEURL."/commenterManager.php");
//    }
//    
//    if(array_key_exists('save', $_POST)){
//        nukeMagicQuotes();
//        $connectionWrite = dbConnect('write', MYDATABASE);
//        foreach ($_POST as $key => $value){
//            ${$key} = trim(mysqli_real_escape_string($connectionWrite, $value));
//        }
//        
//        $sqlCommenterUpdate = "UPDATE commenter SET comt_status = '$status', comt_edited = '$edited', comt_created = '$created' WHERE comt_id = $comtid";
//        $resultCommenterUpdate = $connectionWrite->query($sqlCommenterUpdate);
//        
//        if($resultCommenterUpdate){
//            header("Location: ".SITEURL."/commenterManager.php?result=commenter information has been updated");
//        }
//    } elseif(array_key_exists('cancel', $_POST)) {
//        header("Location: ".SITEURL."/commenterManager.php");
//    }
//}

// #################              queries for userManager page           #####################
if($currentPage == 'userManager.php'){
    $sqlUser = "SELECT user_id, full_name, user_name, user_group, edited, created, user_status FROM user ORDER BY full_name ASC";
    $resultUser = $connection->query($sqlUser) or die (mysqli_error());
}

// #################              queries for userEdit page           #####################
if($currentPage == 'userEdit.php'){
    if(isset ($_GET['userid']) && is_numeric($_GET['userid'])){
        $userId = (int)$_GET['userid'];
        
        $sqlUser = "SELECT * FROM user WHERE user_id = $userId";
        $resultUser = $connection->query($sqlUser) or die (mysqli_error());
        $row = $resultUser->fetch_assoc();
    } else {
        header("Location: ./userManager.php");
    }
    
    if(array_key_exists('submit', $_POST)){
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        if($_SESSION['userlevel'] == 'Power Users'){
            $sqlUserUpdate = "UPDATE user SET user_name = '$username', full_name = '$fullname', user_email = '$useremail', edited = '" . date('Y-m-d H:i:s') . "' WHERE user_id = $userId";
            $redirect_location = './home.php';
            $message = 'your account';
        }
        if($_SESSION['userlevel'] == 'Super Users'){
            if(!empty($password)){
                $sqlUserUpdate = "UPDATE user SET user_name = '$username', full_name = '$fullname', user_email = '$useremail', password = '" . sha1($password) . "', user_group = '$usergroup', user_order = '$userorder', user_status = '$userstatus', edited = '" . date('Y-m-d H:i:s') . "' WHERE user_id = $userId";
            } else {
                $sqlUserUpdate = "UPDATE user SET user_name = '$username', full_name = '$fullname', user_email = '$useremail', user_group = '$usergroup', user_order = '$userorder', user_status = '$userstatus', edited = '" . date('Y-m-d H:i:s') . "' WHERE user_id = $userId";
            }
            $redirect_location = './userManager.php';
            $message = "$fullname's account";
        }
        $resultUserUpdate = $connectionWrite->query($sqlUserUpdate) or die(mysqli_error());
        
        if($resultUserUpdate){
            $_SESSION['message'] = '<div class="alert alert-success" role="alert"><strong>Well done!</strong> ' . $message . ' has been updated successfully.</div>';
            header("Location: $redirect_location");
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> '. $message .' hasn’t been updated. Please try again.</div>';
            header("Location: $redirect_location");
        }
    }
}

// #################              queries for userNew page           #####################
if($currentPage == 'userNew.php'){
    $sqlLastUser = "SELECT user_group, user_order, user_status FROM user ORDER BY user_id DESC LIMIT 1";
    $resultLastUser = $connection->query($sqlLastUser);
    $row = $resultLastUser->fetch_assoc();
    
    if(array_key_exists('save', $_POST) || array_key_exists('savenew', $_POST)){
        if(array_key_exists('savenew', $_POST)){
            $saveNew = true;
        }
        
        nukeMagicQuotes();
        $connectionWrite = dbConnect('write', MYDATABASE);
        foreach ($_POST as $key => $value){
            ${$key} = mysqli_real_escape_string($connectionWrite, trim($value));
        }
        
        $sql = "INSERT INTO user (user_name, full_name, user_email, password, user_group, user_order, user_status, created) VALUES('$username', '$fullname', '$useremail', '" . sha1($password) . "', '$usergroup', $userorder, '$userstatus', '" . date('Y-m-d H:i:s') . "')";
        $result = $connectionWrite->query($sql) or die(mysqli_error());
        
        if($result && isset ($saveNew)){
            $insertResult = "$fullname has been saved.";
            header("Location: ./userNew.php?result=$insertResult");
        } elseif($result && !isset ($saveNew)){
            $insertResult = "$fullname has been saved.";
            header("Location: ./userManager.php?result=$insertResult");
        }
    }
    if(array_key_exists('cancel', $_POST)){
        header("Location: ./userManager.php");
    }
}

// #################              queries for deleteRecord page           #####################
if($currentPage == 'deleteRecord.php'){
    if(array_key_exists('delete', $_POST)){
        $connectionWrite = dbConnect('write', MYDATABASE);
        $recordId = mysqli_real_escape_string($connection, $_GET['recordid']);
        $recordName = mysqli_real_escape_string($connection, $_GET['recordname']);
        
        if(in_array('user', $_POST)){
            $rederictPage = 'userManager.php';
            $sqlDelete = "DELETE FROM user WHERE user_id = $recordId";
            $resultDelete = $connectionWrite->query($sqlDelete) or die(mysqli_error($connection));
        }
        if(in_array('news', $_POST)){
            $rederictPage = 'articleManager.php';
            $sqlDelete = "DELETE FROM news WHERE news_id = $recordId";
            $resultDelete = $connectionWrite->query($sqlDelete) or die(mysqli_error());
        }
        if(in_array('category', $_POST)){
            $rederictPage = 'categoryManager.php';
            $sqlDelete = "DELETE FROM page_names WHERE cat_id = $recordId";
            $resultDelete = $connectionWrite->query($sqlDelete) or die(mysqli_error());
        }
        if(in_array('photos', $_POST)){
            $rederictPage = 'imageManager.php';
            $sqlDelete = "DELETE FROM photos WHERE photo_id = $recordId";
            $resultDelete = $connectionWrite->query($sqlDelete) or die(mysqli_error());
        }
        if(in_array('publicationdate', $_POST)){
            $rederictPage = 'issueManager.php';
            $sqlDelete = "DELETE FROM publicationdate WHERE pd_id = $recordId";
            $resultDelete = $connectionWrite->query($sqlDelete) or die(mysqli_error());
        }
        if(in_array('section', $_POST)){
            $rederictPage = 'sectionManager.php';
            $sqlDelete = "DELETE FROM section WHERE sec_id = $recordId";
            $resultDelete = $connectionWrite->query($sqlDelete) or die(mysqli_error());
        }
        if(in_array('relatedTopics', $_POST)){
            $rederictPage = 'relatedTopicsManager.php';
            $sqlDelete = "DELETE FROM related_toptis WHERE topits_id = $recordId";
            $resultDelete = $connectionWrite->query($sqlDelete) or die(mysqli_error());
        }
        
        if($resultDelete){
            header("Location: ./$rederictPage?result=<strong> $recordName </strong> has been deleted.");
        }
    }
    
    if(array_key_exists('cancel', $_POST)){
        if(in_array('user', $_POST)){
            $rederictPage = 'userManager.php';
        }
        if(in_array('news', $_POST)){
            $rederictPage = 'articleManager.php';
        }
        if(in_array('category', $_POST)){
            $rederictPage = 'categoryManager.php';
        }
        if(in_array('photos', $_POST)){
            $rederictPage = 'imageManager.php';
        }
        if(in_array('publicationdate', $_POST)){
            $rederictPage = 'issueManager.php';
        }
        if(in_array('section', $_POST)){
            $rederictPage = 'sectionManager.php';
        }
        if(in_array('relatedTopics', $_POST)){
            $rederictPage = 'relatedTopicsManager.php';
        }
        header("Location: ./$rederictPage");
    }
}

// #################              queries for userManager page           #####################
if($currentPage == 'userStatistic.php'){
    $sqlUser =
        "SELECT "
        . "news.news_id, "
        . "news.created AS post_created, "
        . "cat_display_name AS page_name "
        . "FROM news, page_names "
        . "WHERE news.news_cat_id = page_names.cat_id "
        . "AND user_id = {$_GET['recordid']} "
        . "ORDER BY news.created DESC "
        . "LIMIT 10";
    $resultUser = $connection->query($sqlUser) or die (mysqli_error($connection));
}