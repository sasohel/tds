<?php     require_once 'includes/session.inc.php';    require_once 'includes/configuration.inc.php';    require_once 'includes/myFunctions.inc.php';    require_once 'includes/connection.inc.php';    require_once './includes/sqlQueries_pfast.inc.php';//    if($_SERVER['REMOTE_ADDR'] == '202.164.211.165'){//        require_once './includes/sqlQueries_pfast.inc.php';//    } else {//        require_once 'includes/sqlQueries.inc.php';//    }?><!DOCTYPE html><html>    <head>        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">        <title>C Panel | Edit Post</title>        <?php require_once 'includes/head_css_js.inc.php'; ?>    </head>    <body>                <?php require_once 'includes/topbar_fixed.inc.php'; ?>                <div class="container">            <div class="row">                <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">                <div class="col-md-12" id="adminNavbar">                    <nav class="navbar navbar-default" role="navigation">                      <div class="container-fluid">                        <!-- Brand and toggle get grouped for better mobile display -->                        <div class="navbar-header">                          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">                            <span class="sr-only">Toggle navigation</span>                            <span class="icon-bar"></span>                            <span class="icon-bar"></span>                            <span class="icon-bar"></span>                          </button>                          <a class="navbar-brand" href="#">Edit post</a>                        </div>                                        <!-- Collect the nav links, forms, and other content for toggling -->                        <div class="collapse navbar-collapse">                            <div class="navbar-form navbar-right">                                <?php                                // submitted value                                if($_SESSION['userlevel'] == 'Super Users'){$submitted = 'submitted to review';}                                elseif($_SESSION['userlevel'] == 'Publisher'){$submitted = 'submitted to review';}                                elseif($_SESSION['userlevel'] == 'Editor (publishing)'){$submitted = 'submitted to review';}                                elseif($_SESSION['userlevel'] == 'Editor'){$submitted = 'submitted to publish';}                                elseif($_SESSION['userlevel'] == 'Power Users'){$submitted = 'submitted to edit';}                                 elseif($_SESSION['userlevel'] == 'User'){$submitted = 'submitted to cook';}                                ?>                                                                <button type="submit" name="submit" class="btn btn-info btn-sm" value="saved">Save</button>                                <button type="submit" name="submit" class="btn btn-info btn-sm" value="save & close">Save &amp; Close</button>                                <button type="submit" name="submit" class="btn btn-info btn-sm" value="save & new">Save &amp; New</button>                                <!-- Publish -->                                <?php if($row['news_status'] != 'published'){ ?>                                                                        <?php if($row['news_status'] == 'saved as draft'){ ?>                                    <button type="submit" name="submit" class="btn btn-primary btn-sm" value="<?php echo $submitted; ?>">Submit</button>                                    <?php } ?>                                                                        <?php if($_SESSION['userlevel'] != 'User'){ ?>                                    <button type="submit" name="submit" class="btn btn-warning btn-sm" value="rejected">Reject</button>                                    <?php } ?>                                                                        <?php if($_SESSION['userlevel'] == 'Super Users' ||                                     $_SESSION['userlevel'] == 'Publisher' || $_SESSION['userlevel'] == 'Editor (publishing)'){ ?>                                    <button type="submit" name="submit" class="btn btn-success btn-sm" value="published">Publish</button>                                    <button type="submit" name="submit" class="btn btn-success btn-sm" value="published & new">Publish &amp; New</button>                                    <?php } ?>                                                                    <?php } ?>                                                                <!-- Withdraw -->                                <?php if($row['news_status'] == 'published'){ ?>                                <button type="submit" name="submit" class="btn btn-warning btn-sm" value="withdrawn">Withdraw</button>                                <?php } ?>                                <a href="./<?php echo $_SESSION['previous_page']; ?>" class="btn btn-danger btn-sm">Close</a>                            </div>                        </div><!-- /.navbar-collapse -->                      </div><!-- /.container-fluid -->                    </nav>                </div>                                <!-- message -->                <?php if(!empty($_SESSION['message'])){ ?>                <div class="col-md-12">                    <?php echo $_SESSION['message']; ?>                </div>                <?php } ?>                                <div class="col-md-12">                    <div class="row">                        <div class="col-md-8">                                                          <div class="form-group">                                <label for="topsubhead" class="col-md-2 control-label">Sub title</label>                                <div class="col-md-10">                                  <input name="topsubhead" type="text" class="form-control" value="<?php echo $row['top_shoulder']; ?>" />                                </div>                              </div>                              <div class="form-group">                                <label for="headline" class="col-md-2 control-label">Title</label>                                <div class="col-md-10">                                  <input name="headline" type="text" class="form-control" required value="<?php echo $row['head_line']; ?>" />                                </div>                              </div>                              <div class="form-group">                                <label for="articledetails" class="col-md-2 control-label">Details</label>                                <div class="col-md-10">                                  <textarea name="articledetails" class="form-control" id="articledetails" rows="20"><?php echo $row['news_details']; ?></textarea>                                </div>                              </div>                        </div>                        <div class="col-md-4">                            <div class="form-group">                                <label for="inputPassword3" class="col-md-4 control-label">Media</label>                                <div class="col-md-8">                                    <?php if(!empty($row['media']['intro_img_name'])){ ?>                                        <img src="./../images/<?php echo $row['media']['intro_img_name']; ?>" alt="..." class="img-thumbnail" />                                    <?php } ?>                                    <input id="intro_media" name="intro_media" type="file" class="file" />                                </div>                            </div>                            <div class="form-group">                                <label for="inputPassword3" class="col-md-4 control-label">Caption</label>                                <div class="col-md-8">                                    <textarea name="intro_media_caption" class="form-control" rows="4"><?php if(!empty($row['media']['intro_img_caption'])) echo $row['media']['intro_img_caption']; ?></textarea>                                </div>                            </div>                            <div class="form-group">                                <label for="inputPassword3" class="col-md-4 control-label">Date</label>                                <div class="col-md-8">                                    <div class="input-group date form_datetime" data-date-format="dd M yyyy hh:ii">                                        <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>                                        <input class="form-control" name="start" type="text" value="<?php echo ($row['publication_date'] != '0000-00-00 00:00:00' && $row['publication_date']) ? date('d M Y H:i', strtotime($row['publication_date'])) : ''; ?>" readonly />                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>                                    </div>                                </div>                            </div>                                                        <div class="form-group">                                <label for="publicationdate" class="col-md-4 control-label">Print date</label>                                <div class="col-md-8">                                    <select name="publicationdate" class="form-control">                                        <option value="">Select a date</option>                                        <?php while($rows = $resultDate->fetch_assoc()){                                            echo '<option value="'.$rows['pd_id'].'"';                                            if($rows['pd_id'] == $row['news_pd_id']){                                                echo ' selected="selected"';                                            }                                            echo '>'.DateFormatConverter($rows['publication_date'] ).'</option>';                                        }                                        ?>                                    </select>                                </div>                            </div>                                                        <div class="form-group">                                <label for="newsorder" class="col-md-4 control-label">Order</label>                                <div class="col-md-8">                                    <input name="newsorder" type="text" class="form-control" value="<?php echo $row['news_order']; ?>" />                                </div>                            </div>                            <div class="form-group"> <!-- section -->                                <label for="inputPassword3" class="col-md-4 control-label">Section</label>                                <div class="col-md-8">                                    <select name="section" id="section" class="form-control" <?php if($_SESSION['name'] != 'rezaur.s.m@gmail.com' && $_SESSION['name'] != 'jubaierbd@gmail.com' && $_SESSION['name'] != 'nsaadi90@gmail.com' && $_SESSION['name'] != 'webadmin@dailysangram.com'){ ?> required <?php } ?>>                                        <option value="">Select a section</option>                                        <?php foreach($sections as $key => $section){ ?>                                            <?php if($key == $row['section_id']){ ?>                                                <option value="<?php echo $key ?>" selected><?php echo $section['title']; ?></option>                                            <?php } else { ?>                                                <option value="<?php echo $key ?>"><?php echo $section['title']; ?></option>                                            <?php } ?>                                        <?php } ?>                                    </select>                                </div>                            </div>                            <div class="form-group"> <!-- category -->                                <label for="inputPassword3" class="col-md-4 control-label">Category</label>                                <div class="col-md-8">                                    <select name="category" class="form-control" id="category" <?php if($_SESSION['name'] != 'rezaur.s.m@gmail.com' && $_SESSION['name'] != 'jubaierbd@gmail.com' && $_SESSION['name'] != 'nsaadi90@gmail.com' && $_SESSION['name'] != 'webadmin@dailysangram.com'){ ?> required <?php } ?>>                                        <option value="">Select a category</option>                                        <?php foreach($categories as $section => $category){ ?>                                            <?php foreach($category as $value){ ?>                                                <?php if($value['id'] == $row['category_id']){ ?>                                                    <option value="<?php echo $value['id'] ?>" class="<?php echo $section; ?>" selected><?php echo $value['title']; ?></option>                                                <?php } else { ?>                                                    <option value="<?php echo $value['id'] ?>" class="<?php echo $section; ?>"><?php echo $value['title']; ?></option>                                                <?php } ?>                                            <?php } ?>                                        <?php } ?>                                    </select>                                </div>                            </div>                                                        <div class="form-group">                                <label for="inputPassword3" class="col-md-4 control-label">Page</label>                                <div class="col-md-8">                                  <select class="form-control" name="page_name">                                    <option value="">Select a page </option>                                    <?php                                    $optgroupLabel = array();                                    while($rows = $resultCategory->fetch_assoc()){                                        $secDisplayName = $rows['sec_display_name'];                                                                                if(!in_array($secDisplayName, $optgroupLabel)){                                            $optgroupLabel[] = $secDisplayName;                                            if(count($optgroupLabel) > 1){                                                echo '</optgroup><optgroup label="'.$secDisplayName.'">';                                            } else {                                                echo '<optgroup label="'.$secDisplayName.'">';                                            }                                        }                                                                                echo '<option value="'.$rows['cat_id'].'"';                                        if($rows['cat_id'] == $row['news_cat_id']){                                            echo 'selected="selected"';                                        }                                        echo '>'.$rows['cat_display_name'].'</option>';                                    }                                    echo '</optgroup>';                                    ?>                                    </select>                                </div>                            </div>                                                        <div class="form-group">                                <label for="frontpage" class="col-md-4 control-label">Front page</label>                                <div class="col-md-8">                                  <label class="radio-inline">                                    <input type="radio" name="frontpage" <?php if($row['front_page'] == 'yes'){ echo 'checked';} ?> value="yes"> Yes                                  </label>                                  <label class="radio-inline">                                    <input type="radio" name="frontpage" <?php if($row['front_page'] == 'no'){ echo 'checked';} ?>  value="no"> No                                  </label>                                </div>                            </div>                            <div class="form-group"> <!-- block -->                                <label for="inputPassword3" class="col-md-4 control-label">Promoted to</label>                                <div class="col-md-8">                                    <select name="block" id="block" class="form-control">                                        <option value="">Select a block</option>                                        <?php while($rows = $result_block->fetch_assoc()){ ?>                                            <?php if($rows['id'] == $row['block_id']){ ?>                                                <option value="<?php echo $rows['id'] ?>" selected><?php echo $rows['name']; ?></option>                                            <?php } else { ?>                                                <option value="<?php echo $rows['id'] ?>"><?php echo $rows['name']; ?></option>                                            <?php } ?>                                        <?php } ?>                                    </select>                                </div>                            </div>                        </div>                    </div>                </div>                </form>            </div>        </div> <!-- /container -->                <!-- clean message session -->        <?php         // var_dump(empty($_POST));        if(!empty($_SESSION['message']) && empty($_POST)){            $_SESSION['message'] = NULL;        }         ?>                <?php require_once 'includes/footer_js.inc.php'; ?>        <!-- TinyMCE -->        <script type="text/javascript" src="assets/js/tinymce_453/js/tinymce/tinymce.min.js"></script>        <script type="text/javascript">            tinymce.init({                selector: "textarea#articledetails",                menubar : false,                plugins: "link,image,preview,code",                toolbar: [                    "styleselect | removeformat | bold italic underline | bullist numlist outdent indent | link unlink image | preview code"                ],                style_formats: [                    { title: 'Inner media', block: 'div', classes: 'innerMedia'}                ]            });        </script>        <!-- /TinyMCE -->        <!--  to Enable/disable category select box -->        <script type="text/javascript">            $("#category").chained("#section");        </script>    </body></html>