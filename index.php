<?php
//if($_SERVER['REMOTE_ADDR'] == '27.147.172.52'){
//    echo 'break';
//    exit;
//}
require_once 'includes/session.inc.php';
//if($_SERVER['REMOTE_ADDR'] != '202.164.211.165'){
//    header("Location: /under_maintenance.html");
//}

require_once 'includes/configuration.inc.php';
require_once './system/library/ubench/Ubench.php';
require_once './system/library/Mobile_Detect/Mobile_Detect.php';
require_once 'classes/Googl.class.php';
require_once 'includes/connection.inc.php';
require_once 'includes/myFunctions.inc.php';
require_once './includes/sqlQueries_pfast.inc.php';
//if($_SERVER['REMOTE_ADDR'] == '202.164.211.165'){
//    require_once './includes/sqlQueries_pfast.inc.php';
//} else {
//    require_once 'includes/sqlQueries.inc.php';
//}
require_once './system/library/onfly_crop/onfly_crop.php';
// short url
$googl = new Googl(SHORT_URL_API_KEY);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo $meta_keywords; ?>" />
<meta name="description" content="<?php echo $meta_description; ?>" />
<title><?php echo $title; ?></title>
<?php if($is_mobile){ ?> <meta name="viewport" content="width=device-width, initial-scale=1"> <?php } ?>
<?php if(isset($facebook_open_graph)){ ?>
<meta property="og:title" content="<?php echo $title; ?>" />
<meta property="og:description" content="<?php echo $og_description; ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="<?php echo $og_url; ?>" />
<meta property="og:site_name" content="The Daily Sangram" />
<meta property="og:image" content="<?php echo $img_social; ?>" />
<meta property="og:image:width" content="<?php echo $img_width; ?>" />
<meta property="og:image:height" content="<?php echo $img_height; ?>" />
<meta property="fb:admins" content="100001148447462" />
<meta property="fb:app_id" content="136532666416171"/>
<?php } ?>

<?php if($is_mobile){ // theme change for mobile or desktop ?>

<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css" />
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-dark-grey.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
<style type="text/css">
    hr{margin: 10px 0;}
    a:link{text-decoration: none;}
    a:hover{color: #5c5c5b;}
    .viewPost h1{font-size: 24px;}
    .dsMarginTop10{margin-top: 10px !important;}
    .dsMarginBottom12{margin-bottom: 12px !important;}
    .innerMedia{margin-left: -16px;margin-right: -16px;}
    .innerMedia img{max-width: 100%; height: auto;}
</style>

<?php } else { ?>

<link href="/assets/contents.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/assets/css/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" media="screen" />
<style type="text/css">
    body{font-family: Arial,SolaimanLipi,sans-serif; font-size: 15px;}
    .supportcontainer{width: 1024px}
    h1{font-size:22px;font-weight:bold;line-height:23px;margin: 0px 0px 10px 0px;padding: 0px;}
	a:link{text-decoration: none; color: #006699;}
    a:visited{color: #006699;}
    a:hover{color: #000000;}
    .content {padding: 20px;}
    .mainContent {background-color: #FFF; text-align: left; font-family: SolaimanLipi;}
    .colLeft {float: left; width: 704px;}
    .colLeft .content{padding-top: 15px; padding-bottom: 0px}
    .colRight {float: left; width: 320px;}
    .marginTop {margin-top: 20px;}
    .colRight h3{margin: 0 0 14px 0; padding: 0px 0px 5px 0px; font-size: 20px; color: #666666; border-bottom: 1px solid #ececec; line-height: 34px;}
    .colRight .content{padding-left: 0px; padding-top: 15px; padding-bottom: 0px;}
    .homeContent h1{margin: 0 0 7px 0; padding: 0px; font-size: 22px;}
    .homeContent h2{margin: 0 0 7px 0; padding: 0px; font-size: 17px;}
    .homeContent h4{margin: 0px; padding: 0px; font-size: 15px; color: #AAAAAA}
    .homeContent img{margin-bottom: 5px;}
    .homeContent ul{margin: 0px; padding: 0px;}
    .homeContent ul li{display: inline-table; width: 317px; padding: 0px; margin: 0 0 20px 0px;}
    .homeContent ul li.odd{margin-left: 20px}
    .homeContent ul li:first-child{width: 100%; margin-left: 0px}
    /*.homeContent ul li img{width: 100%; height: auto}*/

    .lastUpdate{float: none; width: 100%;}
    .lastUpdate ul{margin: 0px; padding: 0px; list-style: none;}
    .lastUpdate ul li{margin-bottom: 5px; border-bottom: 1px solid #DDDDDD; padding: 5px 0px}
    .lastUpdate ul li:last-child{border-bottom: none;}
    .lastUpdate ul li a{font-size: 15px;}

    .archive select{font-size: 15px; padding: 4px 10px; margin-right: 6px; width: 31%}
    .archive select:last-child{margin-right: 0px;}
    .archive input{font-size: 15px; padding: 4px 10px;}
    .archive input[type|=submit]{margin-top: 5px; width: 40%; margin-top: 10px;}

    .leadAndUpdate{font-family: SolaimanLipi;}
    .leadAndUpdate .last_update{padding-right: 0px;}
    .leadAndUpdate .relatedNews fieldset{border-top: 0px solid #DEE6ED;}
    .leadAndUpdate .relatedNews h3{margin-top: 0px; font-family: SolaimanLipi; text-align: left}
    .upddateHead {float: left; width: 215px; margin-left: 10px;}
    .homeLead .content{padding-right: 5px;}

    h3{margin-left: 20px; margin-right: 30px; margin-bottom: 0px; border-bottom: 2px Solid #AAA; font-size: 19px; color:#666666}
    h4{margin: 0px; padding: 0px; font-size: 15px; color: #AAAAAA}
    .dateTime{margin: 0px; font-size: 14px; color: #999;}
    .frsLead{float: left;width: 414px;}
    .frsLead h1{margin-top: 0px;margin-bottom: 5px;}
    .frsLead img{width: 374px;height: 239px;margin-bottom: 8px;}
    .sndLead{float: left;width: 290px;}
    .sndLead img{margin-right: 15px;margin-bottom: 8px;float: left;}
    .sndLead .content {padding-left: 10px;padding-right: 30px;}
    .sndLead .dateTime {display: table-row;}
    .sndLead h2{font-size: 16px;margin-top: 0px;margin-bottom: 5px;}
    .sndLead ul {margin: 0px; padding: 0px; list-style: none;}
    .sndLead ul li{display: inline-table;border-bottom: 1px solid #ddd;padding-bottom: 15px;margin-bottom: 15px; width:100%}
    .sndLead ul li:last-child{border-bottom: none;}
    .thrdLead{width: 704px;}
    .thrdLead img{width: 202px; height: 129px; margin-bottom: 8px;}
    .thrdLead .content {padding-right: 30px;}
    .thrdLead h2{font-size: 16px;margin-top: 0px;margin-bottom: 5px;}
    .thrdLead ul {margin: 0px; padding: 0px;}
    .thrdLead ul li{display: inline-table;width:202px;margin-right:20px;}
    .thrdLead ul li:last-child{margin-right:0px;width:201px;}
    .thrdLead ul li:last-child img{width: 201px;}
    .thrdLead .dateTime {margin:0px;}
    .secLead{float: left;width: 347px;}
    .secLead .content {padding-right: 15px;}
    .secLead h2{margin-top: 0px;margin-bottom: 5px;}
    .secLead img{margin-bottom: 8px;}
    .secOthers{float: left;width: 357px;}
    .secOthers img{margin-right: 15px;margin-bottom: 8px;float: left;}
    .secOthers .content {padding-left: 15px;padding-right: 30px;}
    .secOthers .dateTime {display: table-row;}
    .secOthers h2{font-size: 16px;margin-top: 0px;margin-bottom: 5px;}
    .secOthers ul {margin: 0px; padding: 0px; list-style: none;}
    .secOthers ul li{display: inline-table;border-bottom: 1px solid #ddd;padding-bottom: 15px;margin-bottom: 15px; width:100%}
    .secOthers ul li:last-child{border-bottom: none;}
    .imgThmb img{margin-right: 15px;margin-bottom: 8px;float: left;}
    .imgThmb h2{font-size: 16px;margin-top: 0px;margin-bottom: 5px;}
    .imgThmb .dateTime {display: table-row;}
    .imgThmb h3 {margin-right: 15px;}
    .imgThmb ul {margin: 0px; padding: 0px; list-style: none;}
    .imgThmb ul li{display: inline-table;border-bottom: 1px solid #ddd;padding-bottom: 15px;margin-bottom: 15px; width: 100%}
    .imgThmb ul li:last-child{border-bottom: none;}
    .opinion{margin-top: 20px;}
    .listThumbLess ul {list-style-type: none; margin: 0px; padding: 0px;}
    .listThumbLess ul li {list-style: square; margin-left: 15px; border-bottom: 1px solid #ececec; margin-bottom: 10px; padding-bottom: 10px; padding-left: 5px;}
    .listThumbLess ul li h2 {font-size: 15px; margin: 0px 0px 0px 0px; font-weight: normal;}
    .sectionPageLead{float: left;width: 374px !important;margin-right: 15px;height: 239px !important;}
	.postInfo{color: #999;font-size: 14px;border-bottom: 1px solid #ececec;border-top: 1px solid #ececec;margin-bottom: 15px;padding: 10px 0px;}
	.postsharing{margin-bottom: 15px;}
    .postMedia{background-color: #DDDDDD}
    .mediaContainer{background-color: #EEEEEE; text-align: center;}
    .postMedia img{max-height: 325px; max-width: 664px;}
    .postBody img{max-height: 400px; max-width: 664px;}
    .caption{padding: 5px 10px;}
    .largeThumb img{max-height: 155px; max-width: 317px;}
    .xLargeThumb img{max-height: 196px; max-width: 400px;}
    .alignLeft{float: left;}
    .LargeCol{width:400px;}
    .marginRight{margin-right: 20px;}
    .marginRight15 {margin-right: 15px !important;}
    .marginLeft15 {margin-left: 15px !important;}
    .paginate ul li{width: auto !important; padding: 5px 8px;}

    .menuContainer a:link, .menuContainer a:visited{color:#000000;}
    .mainMenu ul, .mainSubMenu ul {text-align: left; margin:0px}
    .mainMenu ul{background-color: #999999; padding: 10px 15px; font-weight: bold;}
    .mainSubMenu ul{background-color: #eeeeee; padding: 8px 15px;}
    .mainMenu ul li{display: inline; padding: 5px 7px;}
    .mainSubMenu{text-align: left;background-color: #eeeeee;}
    .mainSubMenu ul{}
    .mainSubMenu ul li{display: inline; padding: 5px 7px;}
    .mainSubMenu ul li.bcrmSeparator{padding-left: 0px;padding-right: 0px;}
    .mainSubMenu ul li a{white-space: nowrap;}
    .breadcrumb{display: table-cell;border-right: 1px solid #ffffff;}
    .subMenu{display: table-cell;width:100%}
    .active{color:#ffffff !important;}
    .bcrmSeparatorInner{font-size: 18px;line-height: 10px;font-family: serif;}
    .headerbannermiddleSearch{padding: 6px 0 0;}
    .headerbannermiddleSearchContent{margin-left: 390px;}
    .gsc-control-cse{padding: 0px;}
    .form.gsc-search-box{padding: 0px; margin: 0px;}
    .gsc-results-wrapper-visible{text-align: left;}
    .gsc-control-cse .gs-result .gs-title{margin-bottom: 5px;font-family: SolaimanLipi,sans-serif;font-size: 18px;}
    .gsc-table-cell-snippet-close{font-family: SolaimanLipi,sans-serif;font-size: 14px;}
    .gs-image-box .gs-web-image-box .gs-web-image-box-portrait{width: 100px;height: 60px;}
    .gs-web-image-box-portrait img.gs-image{max-width: 100%; height: auto;}
    .gs-image-box .gs-web-image-box .gs-web-image-box-landscape{width: 100px;height: 60px;}
    .gs-web-image-box-landscape img.gs-image {max-width: 100%;height: auto;}
    .gsc-results-wrapper-overlay{left: 10%; width: 75%;}
</style>
<?php } ?>
</head>

<?php
if($is_mobile){
    include './themes/mobile_w3css/mobile_w3css.php';
} else {
    include './themes/desktop_online/desktop_online.php';
}
?>
</html>