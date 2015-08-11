<?php
/*
 * Copyright Phyo Zaw Tun
 * Licensed under MIT
 * wwww.phyozawtun.com
 * wwww.github.com/poohspear/
 */

// Generate profile image.
function generate_profile_image($q){
    // Get image file name.
    $black_ribbon = "ribbon.png";
    $white_transparant = "white_transparant.png";
    $profile_image = $q;
    // Get image info.
    $black_ribbon_info = getimagesize($black_ribbon);
    $profile_image_info = getimagesize($profile_image);
    // Define flag image object.
    $black_ribbon_obj = imagecreatefrompng($black_ribbon);
    imagealphablending($black_ribbon_obj, true);
    $white_transparant_obj = imagecreatefrompng($white_transparant);
    // Check valid image or not.
    if($profile_image_info === FALSE)
    {
        $content = "<p><span class=\"text text-success\">Your uploaded file is not image file.</span><br />Pleaes try again</p>" . form();
        return page_template($content);
    }
    // Define profile image object.
    switch ($profile_image_info[2]) {
      case IMAGETYPE_GIF  : $profile_image_obj = imagecreatefromgif($profile_image);  break;
      case IMAGETYPE_JPEG : $profile_image_obj = imagecreatefromjpeg($profile_image); break;
      case IMAGETYPE_PNG  : $profile_image_obj = imagecreatefrompng($profile_image);  break;
    }
    imagealphablending($profile_image_obj, true);
    // Get new sizes of flag base on profile image.
    list($width, $height) = $black_ribbon_info;
    list($newwidth, $newheight) = $profile_image_info;
    list($profile_image_width, $profile_image_height) = $profile_image_info;
    echo ("{$newwidth}||{$newheight}");
    if($newwidth<$newheight){
        $newheight = $newwidth/$width*$height;
    }else{
        $newwidth = $newheight/$height*$width;
    }
    // Resizse news flag.
    $new_resize_image = imagecreatetruecolor($newwidth, $newheight);
    imagealphablending($new_resize_image, false);
    imagesavealpha($new_resize_image, true);
    imagecopyresized($new_resize_image, $black_ribbon_obj, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagesavealpha($new_resize_image, true);
    $black_ribbon_obj = $new_resize_image;
    // Combine two image
    $destination_img_obj = $profile_image_obj;
    $source_img_obj = $black_ribbon_obj;
    imagecopy($destination_img_obj, $white_transparant_obj, 0, 0, 0, 0, $profile_image_width, $profile_image_height);
    imagecopy($destination_img_obj, $source_img_obj, 0, 0, 0, 0, $newwidth, $newheight);
    imagesavealpha($destination_img_obj, true);
    $img_name = microtime(true);
    imagepng($destination_img_obj,"./images/{$img_name}.png");
    $content = "
                    <p>
                        <img src=\"./images/{$img_name}.png\" class=\"img-responsive\">
                        <div class=\"clearfix\"></div>
                        <span class=\"text text-success\">Your image save successfully.</span><br />
                        Try with new another photo again
                    </p>" . form();
    return page_template($content);
}
// Main template.
function page_template($content){
    $image_list = image_list();
    return "
    <!DOCTYPE html>
    <html lang=\"en\">
      <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Black Ribbon Movement Myanamr 2015</title>
        <!-- Bootstrap -->
        <link href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\" rel=\"stylesheet\">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src=\"https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js\"></script>
          <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.\"></script>
        <![endif]-->
      </head>
      <body>
        <div class=\"container\">
            <div class=\"col-md-6 col-md-offset-3\">
                <h3>Black Ribbon Movement Myanmar 2015</h3>
                {$content}
                {$image_list}
                <hr />
                Developed for Myanmar People with national spirit and passion at Yangon, Myanmar.<br />
                Developed by poohspear.
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js\"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js\"></script>
      </body>
    </html>
    ";
}
// Submit form.
function form(){
    return "
        <form method=\"post\" action=\"index.php\" enctype=\"multipart/form-data\" role=\"form\">
            <div class=\"form-group\">
                <label>Please choose an image file.</label>
                <input type=file name=\"profile_image\" class=\"form-control\"/>
            </div>
            <div class=\"form-group\">
                <input type=\"submit\" class=\"form-control btn btn-success\" value=\"Generate Image\"/>
            </div>
        </form>
    ";
}
// Function image list
function image_list(){
    $output = ""; 
    $i = 0; 
    if($handle = opendir('./images/')){
        while(false !== ($entry = readdir($handle))) {
            if($entry != "." && $entry != "..") {
                $i++;
                $output .= "<img src=\"./images/{$entry}\" style=\"width:50px\"/>";
            }
        }
        closedir($handle);
    }
    $output = "Campaign is alrady running with <b>{$i}</b> photos.<hr/>{$output}<hr/>";
    return $output;
}

// Define the content
if(!isset($_FILES['profile_image'])){
    $content = form();
    echo page_template($content);
}else{
    $q = $_FILES['profile_image']['tmp_name'];
    echo generate_profile_image($q);
}
?>
