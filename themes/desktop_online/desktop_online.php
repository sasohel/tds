<body>
<div class="container" align="center">
    <div class="supportcontainer">
        <div class="aboutsangram"></div>
        <div class="header">
            <div class="headercontainer">
                <div class="headerleft">
                    <div class="headrelogo"><img src="/logos/logo.jpg" width="175" height="69" alt="The Daily Sangram Logo" /></div>
                </div>
                <div class="headerright">
                    <div class="headerrightcontainer">
                        <div class="headerbannertop">
                            <div class="headerbannertopcontainer">
                                <div class="headerdate">
                                    <?php
                                    if($date_line['description'] == 'NULL'){ echo getBanglaDate($date_line['date']);}
                                    else { echo $date_line['description']; }
                                    ?>
                                </div>
                                <div class="headerslogan">Online Edition</div>
                            </div>
                        </div>
                        <div class="headerbannermiddle">
                            <div class="headerbannermiddleSearch">
                                <div class="headerbannermiddleSearchContent"><?php include('includes/searchengine.inc.php'); ?></div>
                            </div>
                        </div>
                        <div class="headerbanerbottom">
                            <div class="headerbanerbottomcontainer">
                                <div class="headerbanerbottomleft"></div>
                                <div class="headerbanerbottomright"><?php include('includes/header-bottom-menu.inc.php'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="menuContainer">
            <div class="mainMenu"><?php echo $main_menu; ?></div>
            <div class="mainSubMenu">
                <?php echo $breadcrumbs; ?>
                <?php echo $main_sub_menu; ?>
            </div>
        </div>

        <!-- old menue
    <div class="horizontalmenu">
        <?php // include_once 'includes/topCategoryMenu.inc.php'; ?>
    </div>
	-->

        <div class="mainContent">
            <div class="divContainer">
                <div class="colLeft">
                    <?php if(isset($home_lead) || isset($second_lead) || isset($feature_lead)){ ?>
                        <div class="topContent">
                            <div class="fristSecond">
                                <div class="divContainer">
                                    <div class="frsLead">
                                        <div class="content">
                                            <?php
                                            if(isset($home_lead)){
                                                foreach($home_lead as $rows){
                                                    $rows['media'] = unserialize($rows['media']);
                                                    $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
                                                    $pub_datetime = (isset($rows['publication_date']) && $rows['publication_date'] == '0000-00-00 00:00:00' || !isset($rows['publication_date'])) ? FALSE : $rows['publication_date'];
//                                                    $shor_url = $googl->shorten($url);
                                                    $rows['news_details'] = (strpos($rows['news_details'], '<object') == true && strpos($rows['news_details'], 'type="application/x-shockwave-flash"') == true) ?
                                                        substr(strip_tags($rows['news_details'], '<a><div><object><param>'), 0, 500) : $extract = substr(strip_tags($rows['news_details'], '<a>'), 0, 500);
                                                    $lastSpace = strrpos($rows['news_details'], ' ');
                                                    $rows['news_details'] = substr($rows['news_details'], 0, $lastSpace);
                                                    ?>
                                                    <?php if($rows['top_shoulder']){ ?> <h4> <?php echo $rows['top_shoulder']; ?> </h4> <?php } ?>
                                                    <h1><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h1>
                                                    <?php
                                                    if(!empty($rows['media']['intro_img_name'])){
                                                        $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 374, 239);
                                                        if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                            <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="374" height="239" />
                                                        <?php }} ?>

                                                    <p class="news-short-description">
                                                        <?php echo $rows['news_details'];  ?>
                                                        ... ...
                                                    </p>

                                                    <?php if($pub_datetime){ ?>
                                                        <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($pub_datetime, true); ?></p>
                                                    <?php } ?>
                                                <?php }} ?>
                                        </div>
                                    </div>
                                    <div class="sndLead">
                                        <div class="content">
                                            <ul>
                                                <?php
                                                if(isset($second_lead)){
                                                    foreach($second_lead as $rows){
                                                        $rows['media'] = unserialize($rows['media']);
                                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
                                                        $pub_datetime = ($rows['publication_date'] == '0000-00-00 00:00:00' || !$rows['publication_date']) ? FALSE : $rows['publication_date'];
//                                                        $shor_url = $googl->shorten($url);
                                                        ?>
                                                        <li class="even">
                                                            <?php if(!empty($rows['media']['intro_img_name'])){
                                                                $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 95, 80);
                                                                if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                                    <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="95" height="80"/>
                                                                <?php }} ?>
                                                            <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                            <?php if($pub_datetime){ ?>
                                                                <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($pub_datetime, true); ?></p>
                                                            <?php } ?>
                                                        </li>
                                                    <?php }} ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="thrdLead">
                                <div class="content">
                                    <ul>
                                        <?php
                                        if(isset($feature_lead)){
                                            foreach($feature_lead as $rows){
                                                $rows['media'] = unserialize($rows['media']);
                                                $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
                                                $pub_datetime = ($rows['publication_date'] == '0000-00-00 00:00:00' || !$rows['publication_date']) ? FALSE : $rows['publication_date'];
//                                                $shor_url = $googl->shorten($url);
                                                ?>
                                                <li class="even">
                                                    <?php if(!empty($rows['media']['intro_img_name'])){
                                                        $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 202, 129);
                                                        if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                            <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="202", height="129" />
                                                        <?php }} ?>
                                                    <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                    <?php if($pub_datetime){ ?>
                                                        <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($pub_datetime, true); ?></p>
                                                    <?php } ?>
                                                </li>
                                            <?php }} ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="middleContent">
                        <?php if(isset($bangladesh_lead)){ ?>
                            <div class="fristSecond">
                                <h3><a href="/section/bangladesh">বাংলাদেশ</a></h3>
                                <div class="divContainer">
                                    <div class="secLead">
                                        <div class="content">
                                            <?php
                                            foreach($bangladesh_lead as $rows){
                                                $rows['media'] = unserialize($rows['media']);
                                                $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                $shor_url = $googl->shorten($url);
                                                $rows['news_details'] = (strpos($rows['news_details'], '<object') == true && strpos($rows['news_details'], 'type="application/x-shockwave-flash"') == true) ?
                                                    substr(strip_tags($rows['news_details'], '<a><div><object><param>'), 0, 500) : $extract = substr(strip_tags($rows['news_details'], '<a>'), 0, 500);
                                                $lastSpace = strrpos($rows['news_details'], ' ');
                                                $rows['news_details'] = substr($rows['news_details'], 0, $lastSpace);
                                                ?>
                                                <?php if($rows['top_shoulder']){ ?> <h4> <?php echo $rows['top_shoulder']; ?> </h4> <?php } ?>
                                                <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                <?php
                                                if(!empty($rows['media']['intro_img_name'])){
                                                    $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 312, 197);
                                                    if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                        <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="312" height="197" />
                                                    <?php }} ?>
                                                <p class="news-short-description">
                                                    <?php echo $rows['news_details'];  ?>
                                                    ... ...
                                                </p>
                                                <!-- <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> 14 Oct 2016 - 02:22</p> -->
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="secOthers">
                                        <div class="content">
                                            <ul>
                                                <?php
                                                if(isset($bangladesh_others)){
                                                    foreach($bangladesh_others as $rows){
                                                        $rows['media'] = unserialize($rows['media']);
                                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                        $shor_url = $googl->shorten($url);
                                                        ?>
                                                        <li class="even"> <!--
                                            <?php if(!empty($rows['media']['intro_img_name'])){
                                                                $imageFile = './images/'.$rows['media']['intro_img_name'];
                                                                if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                    <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" />
                                            <?php }} ?> -->
                                                            <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                            <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                                        </li>
                                                    <?php }} ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } if(isset($international) || isset($sports)){ ?>
                            <div class="fristSecond">
                                <div class="divContainer">
                                    <div class="secLead">
                                        <h3 class="marginRight15"><a href="/section/international">আন্তর্জাতিক</a></h3>
                                        <div class="content imgThmb">
                                            <?php if(isset($international)){ ?>
                                                <ul>
                                                    <?php
                                                    foreach($international as $rows){
                                                        $rows['media'] = unserialize($rows['media']);
                                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                        $shor_url = $googl->shorten($url);
                                                        ?>
                                                        <li class="even">
                                                            <?php if(!empty($rows['media']['intro_img_name'])){
                                                                $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 95, 80);
                                                                if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                                    <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="95" height="80" />
                                                                <?php }} ?>
                                                            <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                            <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="secOthers">
                                        <h3><a href="/section/sports">খেলা</a></h3>
                                        <div class="content imgThmb">
                                            <?php if(isset($sports)){ ?>
                                                <ul>
                                                    <?php
                                                    foreach($sports as $rows){
                                                        $rows['media'] = unserialize($rows['media']);
                                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                        $shor_url = $googl->shorten($url);
                                                        ?>
                                                        <li class="even">
                                                            <?php if(!empty($rows['media']['intro_img_name'])){
                                                                $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 95, 80);
                                                                if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                                    <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="95" height="80" />
                                                                <?php }} ?>
                                                            <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                            <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } if(isset($life_style_lead)){ ?>
                            <div class="fristSecond">
                                <h3><a href="/section/life-style">লাইফ স্টাইল</a></h3>
                                <div class="divContainer">
                                    <div class="secLead">
                                        <div class="content">
                                            <?php
                                            foreach($life_style_lead as $rows){
                                                $rows['media'] = unserialize($rows['media']);
                                                $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                $shor_url = $googl->shorten($url);
                                                $rows['news_details'] = (strpos($rows['news_details'], '<object') == true && strpos($rows['news_details'], 'type="application/x-shockwave-flash"') == true) ?
                                                    substr(strip_tags($rows['news_details'], '<a><div><object><param>'), 0, 500) : $extract = substr(strip_tags($rows['news_details'], '<a>'), 0, 500);
                                                $lastSpace = strrpos($rows['news_details'], ' ');
                                                $rows['news_details'] = substr($rows['news_details'], 0, $lastSpace);
                                                ?>
                                                <?php if($rows['top_shoulder']){ ?> <h4> <?php echo $rows['top_shoulder']; ?> </h4> <?php } ?>
                                                <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                <?php
                                                if(!empty($rows['media']['intro_img_name'])){
                                                    $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 312, 197);
                                                    if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                        <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="312", height="197" />
                                                    <?php }} ?>
                                                <p class="news-short-description">
                                                    <?php echo $rows['news_details'];  ?>
                                                    ... ...
                                                </p>
                                                <!-- <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> 14 Oct 2016 - 02:22</p> -->
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="secOthers">
                                        <div class="content">
                                            <ul>
                                                <?php
                                                if(isset($life_style_others)){
                                                    foreach($life_style_others as $rows){
                                                        $rows['media'] = unserialize($rows['media']);
                                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                        $shor_url = $googl->shorten($url);
                                                        ?>
                                                        <li class="even">
                                                            <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                            <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                                        </li>
                                                    <?php }} ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } if(isset($feature) || isset($science_technology)){ ?>
                            <div class="fristSecond">
                                <div class="divContainer">
                                    <div class="secLead">
                                        <h3 class="marginRight15"><a href="/section/feature">ফিচার</a></h3>
                                        <div class="content imgThmb">
                                            <?php if(isset($feature)){ ?>
                                                <ul>
                                                    <?php
                                                    foreach($feature as $rows){
                                                        $rows['media'] = unserialize($rows['media']);
                                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                        $shor_url = $googl->shorten($url);
                                                        ?>
                                                        <li class="even">
                                                            <?php if(!empty($rows['media']['intro_img_name'])){
                                                                $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 95, 80);
                                                                if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                                    <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="95" height="80" />
                                                                <?php }} ?>
                                                            <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                            <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="secOthers">
                                        <h3><a href="/section/science-technology">বিজ্ঞান ও প্রযুক্তি</a></h3>
                                        <div class="content imgThmb">
                                            <?php if(isset($science_technology)){ ?>
                                                <ul>
                                                    <?php
                                                    foreach($science_technology as $rows){
                                                        $rows['media'] = unserialize($rows['media']);
                                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                                        $shor_url = $googl->shorten($url);
                                                        ?>
                                                        <li class="even">
                                                            <?php if(!empty($rows['media']['intro_img_name'])){
                                                                $imageFile = onfly_crop('./images/'.$rows['media']['intro_img_name'], 95, 80);
                                                                if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                                    <img src="<?php echo $imageFile; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" width="95" height="80" />
                                                                <?php }} ?>
                                                            <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h2>
                                                            <p class="dateTime"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if(isset($view_post)){?>
                        <div class="content">
                            <div class="postDetails">
                                <?php if($post_head_line){ ?><h1><a src="#"><?php echo $post_head_line; ?></a></h1> <?php } // post_head_line ?>
                                <?php if($post_info ){ ?> <div class="postInfo"><?php echo $post_info; ?> </div> <?php } // publication date, edited ?>

                                <div class="postsharing">
                                    <div class="fb-save" data-uri="<?php echo $post_url; ?>" data-size="small"></div>
                                    <div class="fb-send" data-href="<?php echo $post_url; ?>"></div>
                                    <div class="fb-like" data-href="<?php echo $post_url; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
                                    <div class="g-plus" data-action="share" data-annotation="bubble" data-href="<?php echo $post_url; ?>"></div>
                                    <div style="display: inline-block">
                                        <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $post_url; ?>">Tweet</a>
                                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                                    </div>
                                    <div style="display: inline-block">
                                        <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                                        <script type="IN/Share" data-url="<?php echo $post_url; ?>" data-counter="right"></script>
                                    </div>
                                </div>

                                <?php if(isset($post_img)){ ?>
                                    <div class="postMedia">
                                        <div class="mediaContainer"><img src="<?php echo $post_img; ?>" /></div>
                                        <?php if($img_caption){ ?><div class="caption"><?php echo $img_caption; ?></div> <?php } ?>
                                    </div>
                                <?php } ?>

                                <?php if($post_details){ ?> <div class="postBody"><?php echo $post_details; ?></div> <?php } ?>

                            </div>
                        </div>
                    <?php } elseif($message) { ?>
                        <div class="content">
                            <div class="postDetails">
                                <h1>পাওয়া যায়নি</h1>
                                <?php echo $message ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if(isset($posts)){ ?>
                        <div class="content">
                            <div class="homeContent">
                                <ul>
                                    <?php
                                    $newsCounter = 0;
                                    $newsDisplayed = array();
                                    foreach($posts as $rows){
                                        $rows['media'] = unserialize($rows['media']);
                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                        $shor_url = $googl->shorten($url);
                                        $newsCounter ++;
                                        $publicationDate = null;
                                        if($newsCounter == 1){$maxWords = is_null($publicationDate) ? 1100 : ($publicationDate > '2009-06-30' ? 1150 : 3600);?>
                                            <li class="<?php  echo ($newsCounter % 2 == 0) ? "even" : "odd"; ?>">
                                                <?php if($rows['top_shoulder']){ ?> <h4> <?php echo $rows['top_shoulder']; ?> </h4> <?php } ?>
                                                <h1><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a></h1>
                                                <?php
                                                if(!empty($rows['media']['intro_img_name'])){
                                                    $imageFile = './images/'.$rows['media']['intro_img_name'];
                                                    $imageSource = '/images/'.$rows['media']['intro_img_name'];
                                                    if(file_exists($imageFile) && is_readable($imageFile)){ ?>
                                                        <div class="mediaContainer alignLeft LargeCol marginRight">
                                                            <div class="xLargeThumb">
                                                                <img src="<?php echo $imageSource; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" />
                                                            </div>
                                                        </div>
                                                    <?php }} ?>
                                                <p class="news-short-description">
                                                    <?php
                                                    if(strpos($rows['news_details'], '<object') == true && strpos($rows['news_details'], 'type="application/x-shockwave-flash"') == true){
                                                        $extract = substr(strip_tags($rows['news_details'], '<a><div><object><param>'), 0, $maxWords);
                                                    } else {
                                                        $extract = substr(strip_tags($rows['news_details'], '<a>'), 0, $maxWords);
                                                    }
                                                    $lastSpace = strrpos($extract, ' ');
                                                    echo substr($extract, 0, $lastSpace);
                                                    $newsDisplayed[] = $rows['news_id'];
                                                    ?>
                                                    ... ...
                                                </p>
                                                <p class="news-short-description" style="border: #FF0000 0px solid; text-align: right; font-size: 13px;">
                                                    <?php if($authinticeted){ ?> <a href="./how-to-log/articleEdit.php?news_id=<?php echo $rows['news_id']; ?>" class="lyteframe" data-title="C Panel | Edit Article" data-lyte-options="width:80% height:90% scrolling:no" rel="nofollow" id="news-detail">Edit</a> | <?php } ?>
                                                    <a href="<?php echo $url; ?>" rel="nofollow" id="news-detail">বিস্তারিত দেখুন</a>
                                                </p>
                                            </li>
                                        <?php }
                                        else { $maxWords = !$publicationDate ? 1000 : ($publicationDate > '2009-06-30' ? 1000 : 2000);
                                            if(!in_array($rows['news_id'], $newsDisplayed)){
                                                ?>
                                                <li class="<?php  echo ($newsCounter % 2 == 0) ? "even" : "odd"; ?>">
                                                    <?php if($rows['top_shoulder']){ ?><h4> <?php echo $rows['top_shoulder']; ?> </h4>
                                                    <?php } ?>
                                                    <h2><a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" id="news-detail"> <?php echo $rows['head_line']; ?> </a></h2>
                                                    <?php
                                                    if($rows['media']['intro_img_name']){
                                                        $imageFile = './images/'.$rows['media']['intro_img_name'];
                                                        $imageSource = '/images/'.$rows['media']['intro_img_name'];
                                                        if(file_exists($imageFile) && is_readable($imageFile)){
                                                            $maxWords = $maxWords - 700;
                                                            ?>
                                                            <div class="largeThumb">
                                                                <div class="mediaContainer">
                                                                    <img src="<?php echo $imageSource; ?>" alt="<?php echo $rows['head_line']; ?>" title="<?php echo $rows['head_line']; ?>" />
                                                                </div>
                                                            </div>
                                                        <?php }} ?>
                                                    <p class="news-short-description">
                                                        <?php
                                                        $extract = substr(strip_tags($rows['news_details'], '<a>'), 0, $maxWords);
                                                        $lastSpace = strrpos($extract, ' ');
                                                        echo substr($extract, 0, $lastSpace);
                                                        $newsDisplayed[] = $rows['news_id'];
                                                        ?>
                                                        ... ...
                                                    </p>
                                                    <p class="news-short-description" style="text-align: right">
                                                        <?php if($authinticeted){ ?> <a href="./how-to-log/articleEdit.php?news_id=<?php echo $rows['news_id']; ?>" class="lyteframe" data-title="C Panel | Edit Article" data-lyte-options="width:80% height:90% scrolling:no" rel="nofollow" id="news-detail">Edit</a> | <?php } ?>
                                                        <a href="<?php echo $url; ?>" rel="nofollow" id="news-detail">বিস্তারিত দেখুন</a>
                                                    </p>

                                                </li>
                                            <?php }}} ?>
                                </ul>
                                <?php
                                if(isset($pagination)):
                                    $previous_li = $previous ? '<a href="' . SITEURL . $pagination_uri . '/' . $previous . '">আগে</a>' : 'আগে';
                                    $next_li = $next ? '<a href="' . SITEURL . $pagination_uri . '/' . $next . '">পরে</a>' : 'পরে';
                                    $first_page_li = $first_page ? '<a href="' . SITEURL . $pagination_uri . '/' . $first_page . '">প্রথম পৃষ্ঠা</a>' : 'প্রথম পৃষ্ঠা';
                                    $last_page_li = $last_page ? '<a href="' . SITEURL . $pagination_uri . '/' . $last_page . '">শেষ পৃষ্ঠা</a>' : 'শেষ পৃষ্ঠা';
                                    ?>
                                    <div class="paginate">
                                        <ul>
                                            <li><?php echo $first_page_li; ?></li>
                                            <li><?php echo $previous_li; ?></li>
                                            <?php
                                            for($i = 1; $i <= $total_pages; $i++){
                                                if($i >= $link_start && $i <= $link_end) {
                                                    if($page_no == $i){
                                                        echo '<li class="current">';
                                                        echo convertEngNumberInbangla($i);
                                                        echo '</li>';
                                                    } else {
                                                        echo '<li><a href="' . SITEURL . $pagination_uri . '/' . $i . '">';
                                                        echo convertEngNumberInbangla($i);
                                                        echo '</a></li>';
                                                    }
                                                }
                                            }
                                            ?>
                                            <li><?php echo $next_li; ?></li>
                                            <li><?php echo $last_page_li; ?></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="colRight">
                    <div class="content" id="scrollFixedRight">
                        <?php if($latest_news): ?>
                            <div class="lastUpdate listThumbLess">
                                <h3>অনলাইন আপডেট</h3>
                                <ul>
                                    <?php
                                    foreach($latest_news as $rows){
                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                        $shor_url = $googl->shorten($url);
                                        ?>
                                        <li>
                                            <h2>
                                                <a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a>
                                            </h2>
                                            <p class="dateTime"><?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="dsFacebook marginTop">
                            <div class="fb-page" data-href="https://www.facebook.com/dailysangram/" data-width="300" data-small-header="false" data-adapt-container-width="false" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/dailysangram/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/dailysangram/">Daily Sangram</a></blockquote></div>
                        </div>
                        <!--                    <div class="googlePlus marginTop">-->
                        <!--                        <g:plus href="https://plus.google.com/104290651365737197932" size="badge"></g:plus>-->
                        <!--                    </div>-->

                        <?php if(isset($opinion)){ ?>
                            <div class="opinion listThumbLess">
                                <h3><a href="/section/opinion">মতামত</a></h3>
                                <ul>
                                    <?php
                                    foreach($opinion as $rows){
                                        $url = SITEURL . '/post/' . $rows['news_id'] . '-' . make_alias($rows['head_line'], $removable_characters);
//                                        $shor_url = $googl->shorten($url);
                                        ?>
                                        <li>
                                            <h2>
                                                <a href="<?php echo $url; ?>" rel="dofollow" title="<?php echo $rows['head_line']; ?>" > <?php echo $rows['head_line']; ?> </a>
                                            </h2>
                                            <p class="dateTime"><?php echo getBanglaDate($rows['publication_date'], true); ?></p>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>

                        <div class="archive marginTop">
                            <h3>আর্কাইভ</h3>
                            <form id="archiveselector" name="archiveselector" method="post" action="index.php">
                                <div>
                                    <select id="archiveday" name="archiveday">
                                        <option value="" disabled selected>দিন</option>
                                        <option value="01">০১</option>
                                        <option value="02">০২</option>
                                        <option value="03">০৩</option>
                                        <option value="04">০৪</option>
                                        <option value="05">০৫</option>
                                        <option value="06">০৬</option>
                                        <option value="07">০৭</option>
                                        <option value="08">০৮</option>
                                        <option value="09">০৯</option>
                                        <option value="10">১০</option>
                                        <option value="11">১১</option>
                                        <option value="12">১২</option>
                                        <option value="13">১৩</option>
                                        <option value="14">১৪</option>
                                        <option value="15">১৫</option>
                                        <option value="16">১৬</option>
                                        <option value="17">১৭</option>
                                        <option value="18">১৮</option>
                                        <option value="19">১৯</option>
                                        <option value="20">২০</option>
                                        <option value="21">২১</option>
                                        <option value="22">২২</option>
                                        <option value="23">২৩</option>
                                        <option value="24">২৪</option>
                                        <option value="25">২৫</option>
                                        <option value="26">২৬</option>
                                        <option value="27">২৭</option>
                                        <option value="28">২৮</option>
                                        <option value="29">২৯</option>
                                        <option value="30">৩০</option>
                                        <option value="31">৩১</option>
                                    </select>
                                    <select id="archivemonth" name="archivemonth">
                                        <option value="" disabled selected>মাস</option>
                                        <option value="01">জানুয়ারি</option>
                                        <option value="02">ফেব্রুয়ারি</option>
                                        <option value="03">মার্চ</option>
                                        <option value="04">এপ্রিল</option>
                                        <option value="05">মে</option>
                                        <option value="06">জুন</option>
                                        <option value="07">জুলাই</option>
                                        <option value="08">আগষ্ট</option>
                                        <option value="09">সেপ্টেম্বর</option>
                                        <option value="10">অক্টোবর</option>
                                        <option value="11">নভেম্বর</option>
                                        <option value="12">ডিসেম্বর</option>
                                    </select>
                                    <select id="archiveyear" name="archiveyear">
                                        <option value="" disabled selected>বছর</option>
                                        <option value="2017">২০১৭</option>
                                        <option value="2016">২০১৬</option>
                                        <option value="2015">২০১৫</option>
                                        <option value="2014">২০১৪</option>
                                        <option value="2013">২০১৩</option>
                                        <option value="2012">২০১২</option>
                                        <option value="2011">২০১১</option>
                                        <option value="2010">২০১০</option>
                                    </select>
                                </div>
                                <div><input type="submit" name="archvesearch" value="দেখুন" /> </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div id="footercontainer">
                <div class="footerleft">
                    <div class="footerleftcontent"><?php include_once 'includes/footer.inc.php'; ?></div>
                </div>
                <div class="footerright">
                    <div class="footerrightcontent"><?php include_once 'includes/footer_right.inc.php'; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=136532666416171";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<link href="https://plus.google.com/104290651365737197932" rel="publisher" />
<script type="text/javascript">
    (function()
    {var po = document.createElement("script");
        po.type = "text/javascript"; po.async = true;po.src = "https://apis.google.com/js/plusone.js";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(po, s);
    })();</script>
<?php include_once 'includes/googleAnalytics.inc.php'; ?>
</body>