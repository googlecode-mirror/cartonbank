<style>
.tdc2{
	text-align:center; 
	line-height:1em;
	background-color:#FFFFFF; 
	padding:2px;
}
td.r{
	text-align: right;
	vertical-align: top;
	width: 150px;
}
td.ralt{
	text-align: right;
	background-color:#FFFF00;
	vertical-align: top;
}
td.lalt{
	text-align: left;
	background-color:#FFFF00;
	vertical-align: top;
}
</style>
<?php 
    // Filter for authors
    // pokazh($current_user->wp_capabilities['author'],"wp_capabilities"); // Автор 
    // pokazh($current_user->wp_capabilities['administrator'],"wp_capabilities"); Админ

    global $user_brand;

	if ($product['id'] != $current_user->id & $current_user->wp_user_level < 2)
	{
		echo ('Извините, у вас недостаточно полномочий для доступа к этой странице.');
		exit();
	}

    $_SITEURL = get_option('siteurl');

    if (isset($current_user->ID) && is_numeric($current_user->ID))
    {
        $user_id = $current_user->ID;
        $sql = "SELECT * FROM `wp_product_brands` where user_id=".$user_id;
        $user_brand = $wpdb->get_results($sql,ARRAY_A);
    }

    if (isset($user_brand[0]['id']))
    {
        $user_brand = $user_brand[0]['id'];
    } 
    else
    {
        $user_brand = 0;
    }

    $author_group_sql = " AND `wp_product_list`.`brand` = '".$user_brand."' ";

    if (isset($current_user->wp_capabilities['administrator']))
    {
        $author_group_sql = "";
    }
    if (isset($current_user->wp_capabilities['editor']))
    {
        $author_group_sql = "";
    }


    $category_data = null;
    $basepath = str_replace("/wp-admin", "" , getcwd()); 
    $basepath = str_replace("\\wp-admin", "" , $basepath);

    $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
    $product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
    $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";

    $preview_clips_dir = $basepath."/wp-content/plugins/wp-shopping-cart/preview_clips/";
    $image = '';
    global $authors;

    // update preview
    if (isset($_GET['updateimage']))
    {

        $id = $_GET['updateimage'];
        $fileid_data = $wpdb->get_results("SELECT `file` FROM `wp_product_list` WHERE `id` = '$id' LIMIT 1",ARRAY_A);
        $fileid = $fileid_data[0]['file'];
        $file_data = $wpdb->get_results("SELECT * FROM `wp_product_files` WHERE `id` = '$fileid' LIMIT 1",ARRAY_A);
        $idhash = $file_data[0]['idhash'];


        if (file_exists($filedir.$idhash))
        {
            $mimetype = $file_data[0]['mimetype'];
            $filename = $file_data[0]['filename'];

            $height = get_option('product_image_height');
            $width  = get_option('product_image_width');

            $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
            $product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
            $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";

            copy($filedir.$idhash, $imagedir.$filename); // icon
            copy($filedir.$idhash, $product_images.$filename); // preview

            $imgsize = getimagesize($product_images.$filename);
            $file_w = $imgsize[0];
            $file_h = $imgsize[1];

            //ales here we replace slides to that from LG
            $chwidth = get_option('lg_pictwidth'); // crop size
            $chheight = get_option('lg_pictheight'); // crop size
            //$thatdir = $product_images; //destination dir
            $ifolder = ''; //subfolder for artist
            $file = $filename; //
            $resample_quality = 100; //image quality

            // slide
            al_create_resized_file($chwidth, $chheight, $product_images, $ifolder, $file, $resample_quality);    

            // watremark
            $wm = $basepath."/img/watermark.png";
            wtrmark($product_images.$file,$wm);

            // icon
            al_create_cropped_file(140, 140, $imagedir, $ifolder, $file, $resample_quality);      
        }
        else
        {
            echo "<div class='error'><b>WARNING:</b> original file is not found at: ".$filedir.$idhash." <br /></div>";
        }
    }

    // add product
    if(isset($_POST['submit_action']) && $_POST['submit_action'] == 'add') {

        if($_FILES['file']['name'] != null)  {

            //upload_and_resize_and_watermark_images();    

            //transliterate file
            $_FILES['file']['name'] = rus2translit($_FILES['file']['name']); 
            //rename the file  
            $_FILES['file']['name'] = uniqid('', true).$_FILES['file']['name'];
            //ales default upload
            if(!is_dir($product_images))
            {
                mkdir($product_images);
            }
            if(function_exists("getimagesize"))
            {
                switch(isset($_POST['image_resize']) && $_POST['image_resize'])
                {
                    case 2:
                        $height = $_POST['height'];
                        $width  = $_POST['width'];
                        break;

                    default:
                        $height = get_option('product_image_height');
                        $width  = get_option('product_image_width');
                        break;
                }
                copy($_FILES['file']['tmp_name'], $product_images.$_FILES['file']['name']);
                copy($_FILES['file']['tmp_name'], $imagedir.$_FILES['file']['name']);

                $imgsize = getimagesize($product_images.$_FILES['file']['name']);
                $file_w = $imgsize[0];
                $file_h = $imgsize[1];

                //ales here we replace slides to that from LG
                $chwidth = get_option('lg_pictwidth'); // crop size
                $chheight = get_option('lg_pictheight'); // crop size
                $thatdir = $product_images; //destination dir
                $ifolder = ''; //subfolder for artist
                $file = $_FILES['file']['name']; //
                $resample_quality = 100; //image quality

                al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);    
                $wm = $basepath."/wp-content/plugins/wp-shopping-cart/images/watermark.png";
                wtrmark($thatdir.$file,$wm);

                // ales here we replace thumbs to that from LG 
                $chwidth = $width; // crop size
                $chheight = $height; // crop size
                $thatdir = $imagedir; //destination dir

                al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);      
                $image = $wpdb->escape($_FILES['file']['name']);

                /// ales 
            }
            else {
                move_uploaded_file($_FILES['file']['tmp_name'], ($imagedir.$_FILES['file']['name']));
                $image = $wpdb->escape($_FILES['file']['name']);
            }
            ///ales

            $timestamp = time();
            $wpdb->query("INSERT INTO `wp_product_files` ( `id` , `filename`  , `mimetype` , `idhash` , `date` , `width`, `height`) VALUES ( '' , '', '', '', '$timestamp', '', '');");
            $fileid_raw = $wpdb->get_results("SELECT `id` FROM `wp_product_files` WHERE `date` = '$timestamp'",ARRAY_A);
            $fileid = $fileid_raw[0]['id'];
            $idhash = sha1($fileid);
            $mimetype = $_FILES['file']['type'];
            $splitname = explode(".",$_FILES['file']['name']);
            $splitname = array_reverse($splitname);
            $filename = $_FILES['file']['name'];

            if(move_uploaded_file($_FILES['file']['tmp_name'],($filedir.$idhash)))
            {
                $wpdb->query("UPDATE `wp_product_files` SET `filename` = '".$filename."', `mimetype` = '$mimetype', `idhash` = '$idhash', `width` = '$file_w', `height` = '$file_h' WHERE `id` = '$fileid' LIMIT 1");
            }
            $file = $fileid;
        }
        else
        {
            $file = '0';
        }

        if(isset($_POST['display_frontpage']) && $_POST['display_frontpage'] == "yes")
        {
            $display_frontpage = 1;
        }
        else
        {
            $display_frontpage = 0;
        }

        $temadnya = '0';
        $visible = '0';
        $approved = Null;


        if (isset($_POST['temadnya']) && $_POST['temadnya'] == 'on')
        {$temadnya = '1';} else {$temadnya = '0';}  
        if (isset($_POST['approved']) && $_POST['approved'] == 'on')
        {$approved = '1';} else {$approved = '0';}  
        if (isset($_POST['visible']) && $_POST['visible'] == 'on')
            $visible = '1';  
        if (isset($_POST['colored']) && $_POST['colored'] == 'on'){$colored = '1'; }
        else{$colored="0";}
        if (isset($_POST['not_for_sale']) && $_POST['not_for_sale'] == 'on'){$not_for_sale = '1'; }
        else{$not_for_sale="0";}
        if (isset($_POST['portfolio']) && $_POST['portfolio'] == 'on'){$portfolio = '1'; }
        else{$portfolio="0";}

        if (isset($_POST['license1']) && $_POST['license1'] == 'on'){$license1 = '1'; }
        else{$license1="0";}
        if (isset($_POST['license2']) && $_POST['license2'] == 'on'){$license2 = '1'; }
        else{$license2="0";}
        if (isset($_POST['license3']) && $_POST['license3'] == 'on'){$license3 = '1'; }
        else{$license3="0";}




        // TODO: take it off to the SQL table
        // License prices
        // Category group 1
        $l1_price_cat1_default = 250;
        $l2_price_cat1_default = 500;
        $l3_price_cat1_default = 2500;
        // Category group 2
        $l1_price_cat2_default = 250;
        $l2_price_cat2_default = 500;
        $l3_price_cat2_default = 2500;
        //UPDATE `wp_product_list` SET l1_price = 200 WHERE id in (SELECT product_id from `wp_item_category_associations` WHERE category_id in (4,14,5,11));

        $category_id = $wpdb->escape($_POST['category'][0]);

        /*
        pokazh($category_id,"category_id");
        pokazh($_POST['category'],"_POST['category']");
        */
        switch($category_id)
        {
            case 4:
            case 14:
            case 5:
            case 11:
                $l1_price = $l1_price_cat1_default;
                $l2_price = $l2_price_cat1_default;
                $l3_price = $l3_price_cat1_default;
                break;

            case 13:
            case 8:
            case 15:
            case 6:
                $l1_price = $l1_price_cat2_default;
                $l2_price = $l2_price_cat2_default;
                $l3_price = $l3_price_cat2_default;
                break;

            default:
                $l1_price = $l1_price_cat1_default;
                $l2_price = $l2_price_cat1_default;
                $l3_price = $l3_price_cat1_default;
                break;
        }

        // unset not available licences
        if (!isset($license1)||$license1=='0'){$l1_price = '0';}
        if (!isset($license2)||$license2=='0'){$l2_price = '0';}
        if (!isset($license3)||$license3=='0'){$l3_price = '0';}


        if (isset($_POST['brand']) && is_numeric($_POST['brand']))
        {$_brand = $wpdb->escape($_POST['brand']);}
        else {$_brand = $user_brand;}

        $insertsql = "INSERT INTO `wp_product_list` ( `id`, `name`, `description`, `additional_description`, `file` , `image` , `category`, `brand`, `display_frontpage`, `visible`, `approved`, `color`, `not_for_sale`, `l1_price`, `l2_price`, `l3_price`) VALUES ('', '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."', '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."', '".$wpdb->escape(correct_comma(removeCrLf(htmlspecialchars($_POST['additional_description']))))."','".$file."', '".$image."', '".$category_id."', '".$_brand."', '$display_frontpage', '$visible', NULL, '$colored', '$not_for_sale', $l1_price, $l2_price, $l3_price);";

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";

        //mail($to, $subject, $message, $headers);
        //mail("igor.aleshin@gmail.com","new cartoon added",print_r($insertsql,true),$headers);

        if($wpdb->query($insertsql))
        {
            $new_id = mysql_insert_id();
            $product_id = $new_id;

            //mail("igor.aleshin@gmail.com","добавлена картинка ".$new_id,$new_id);

            $sql_purgery = "insert into al_editors_votes (image_id, up, down) values ('".$new_id."','0','0')";
            $wpdb->query($sql_purgery);

            if (get_option('editors_notification_send')=='1') 
            {
				


				// aleshin
                $Current_ID = 16;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("igor.aleshin@gmail.com","Новая картинка в Прихожей!",$votecontent,$headers);

                // bogorad
                $Current_ID = 11;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("vbogorad@mail.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // shilov
                $Current_ID = 21;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("vfshilov@gmail.com","Новая картинка в Прихожей!",$votecontent,$headers);

                // popov
                $Current_ID = 90;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("popov.a.a@bk.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // alexandrov
                $Current_ID = 2;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("Alexandrov_Vasil@mail.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // elkin
                $Current_ID = 38;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("elkin99@yandex.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // stepanov
                $Current_ID = 62;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("stevladimir@yandex.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // sergeev
                $Current_ID = 28;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("aserg48@gmail.com","Новая картинка в Прихожей!",$votecontent,$headers);

                // smagin
                $Current_ID = 23;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("m_smagin@mail.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // samonenko
                $Current_ID = 59;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("samonenko@cniimf.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // bond
                $Current_ID = 41;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("dbond2000@mail.ru","Новая картинка в Прихожей!",$votecontent,$headers);

                // dubinin
                $Current_ID = 109;
                $votecontent = "<html><head><title>Please vote!</title></head><body><a href='".$_SITEURL."/wp-admin/admin.php?page=purgatory/purgatory.php'>Пройти в Прихожую</a><br /><b>".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."</b><br />".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."<br />[".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."]<br /><img src='".$_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$image."'> <br /><br /> <div> Проголосуйте за рисунок:<br/> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=5' target='_blank'>5 - отлично</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=4' target='_blank'>4 - хорошо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=3' target='_blank'>3 - годится</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=2' target='_blank'>2 - слабо</a> <a style='display:block;padding:4px;margin:4px;size:24px;' href='".$_SITEURL."/wp-content/plugins/purgatory/moder_vote.php?ip=".$Current_ID."&id=".$new_id."&vote=1' target='_blank'>1 - плохо</a> </div> </body></html>";
                mail("valdubinin@gmail.com","Новая картинка в Прихожей!",$votecontent,$headers);
						
			}

            if(isset ($_FILES['extra_image']) && ($_FILES['extra_image'] != null) && function_exists('edit_submit_extra_images'))
            {
                $var = edit_submit_extra_images($product_id);
            }

            $variations_procesor = new nzshpcrt_variations;
            if(isset($_POST['variation_values']) && $_POST['variation_values'] != null)
            {
                $variations_procesor->add_to_existing_product($product_id,$_POST['variation_values']); 
            }

            $counter = 0;
            $item_list = '';
            if(count($_POST['category']) > 0)
            {
                foreach($_POST['category'] as $category_id)
                {
                    $sql_delete = "DELETE `wp_item_category_associations`.* FROM `wp_item_category_associations` WHERE `product_id` = '".$product_id."'";
                    $wpdb->query($sql_delete);
                    $wpdb->query("INSERT INTO `wp_item_category_associations` ( `id` , `product_id` , `category_id` ) VALUES ('', '".$product_id."', '".$category_id."');");        
                }
            }
            else
            {
                $default_cat = get_option('default_category');
                $check_existing = $wpdb->get_var("SELECT `id` FROM `wp_item_category_associations` WHERE `product_id` = ".$product_id." LIMIT 1");
                if($check_existing == null)
                {
                    $wpdb->query("INSERT INTO `wp_item_category_associations` ( `id` , `product_id` , `category_id` ) VALUES ('', '".$product_id."', '".$default_cat."');");        
                }
            }

            // insert temadnya
            if ($temadnya == '1') // insert category 777
            {
                $sql_temadnya = "insert into `wp_item_category_associations` (product_id, category_id) values ('".$product_id."','777')";
                $res = $wpdb->query($sql_temadnya);
            }


            $display_added_product = "filleditform(".$product_id.");";

            echo "<div class='updated'><p align='center'>Файл добавлен</p></div>";
        }
        else
        {
            echo "<div class='updated'><p align='center'>Файл не добавлен!</p></div>";
        }
    }

    if(isset($_GET['submit_action']) && $_GET['submit_action'] == "remove_set")
    {
        if(is_numeric($_GET['product_id']) && is_numeric($_GET['variation_assoc_id']))
        {
            $product_id = $_GET['product_id'];
            $variation_assoc_id = $_GET['variation_assoc_id'];
            $variation_association = $wpdb->get_results("SELECT * FROM `wp_variation_associations` WHERE `id` = '$variation_assoc_id' LIMIT 1",ARRAY_A);
            if($variation_association != null)
            {
                $variation_association = $variation_association[0];
                $variation_id = $variation_association['variation_id'];
                $delete_variation_sql = "DELETE FROM `wp_variation_associations` WHERE `id` = '$variation_assoc_id' LIMIT 1";
                $delete_value_sql = "DELETE FROM `wp_variation_values_associations` WHERE `product_id` = '$product_id' AND `variation_id` = '$variation_id'";
                $wpdb->query($delete_variation_sql);
                $wpdb->query($delete_value_sql);
                echo "<div class='updated'><p align='center'>".TXT_WPSC_PRODUCTHASBEENEDITED."</p></div>";
            }
        } 
    }

    // edit product 
    if(isset($_POST['submit_action']) && $_POST['submit_action'] == "edit")
    {
        //transliterate file
        $_FILES['file']['name'] = rus2translit($_FILES['file']['name']); 
        //rename the file  
        $_FILES['file']['name'] = uniqid('', true).$_FILES['file']['name'];

        $id = $_POST['prodid'];
        if(function_exists('edit_submit_extra_images'))
        {
            if(($_FILES['extra_image'] != null))
            {
                $var = edit_submit_extra_images($id);
            }
        }
        if(function_exists('edit_extra_images'))
        {
            $var = edit_extra_images($id);
        }
        //$basepath = str_replace("/wp-admin", "" , getcwd()); this defined at the top of the page
        $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
        $product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
        $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";
        $preview_clips_dir = $basepath."/wp-content/plugins/wp-shopping-cart/preview_clips/"; 

        if(($_FILES['file']['tmp_name'] != null) && ($_FILES['file']['name'] != null))
        {
            $id = $_POST['prodid'];
            $fileid_data = $wpdb->get_results("SELECT `file` FROM `wp_product_list` WHERE `id` = '$id' LIMIT 1",ARRAY_A);
            $fileid = $fileid_data[0]['file'];
            $file_data = $wpdb->get_results("SELECT `id`,`idhash` FROM `wp_product_files` WHERE `id` = '$fileid' LIMIT 1",ARRAY_A);
            $idhash = $file_data[0]['idhash'];
            $mimetype = $_FILES['file']['type'];

            $filename = $_FILES['file']['name'];

            if(!is_dir($product_images))
            {
                mkdir($product_images);
            }
            if(function_exists("getimagesize"))
            {
                switch(isset ($_POST['image_resize']) && $_POST['image_resize'])    
                {
                    case 2:
                        $height = $_POST['height'];
                        $width  = $_POST['width'];
                        break;

                    default:
                        $height = get_option('product_image_height');
                        $width  = get_option('product_image_width');
                        break;
                }
                copy($_FILES['file']['tmp_name'], $product_images.$_FILES['file']['name']);
                copy($_FILES['file']['tmp_name'], $imagedir.$_FILES['file']['name']);

                $imgsize = getimagesize($product_images.$_FILES['file']['name']);
                $file_w = $imgsize[0];
                $file_h = $imgsize[1];

                //ales here we replace slides to that from LG
                $chwidth = get_option('lg_pictwidth'); // crop size
                $chheight = get_option('lg_pictheight'); // crop size
                $thatdir = $product_images; //destination dir
                $ifolder = ''; //subfolder for artist
                $file = $_FILES['file']['name']; //
                $resample_quality = 100; //image quality

                al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);    
                $wm = $basepath."/wp-content/plugins/wp-shopping-cart/images/watermark.png";
                wtrmark($thatdir.$file,$wm);

                // ales here we replace thumbs to that from LG 
                $chwidth = $width; // crop size
                $chheight = $height; // crop size
                $thatdir = $imagedir; //destination dir

                al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);      
                $image = $wpdb->escape($_FILES['file']['name']);

                /// ales 


            }
            else {
                move_uploaded_file($_FILES['file']['tmp_name'], ($imagedir.$_FILES['file']['name']));
                $image = $wpdb->escape($_FILES['file']['name']);
            }
            //include("image_processing.php");

				//pokazh($_FILES['file']['tmp_name'],"tmp_name");
				//pokazh($imagedir.$_FILES['file']['name'],"name");

			if(file_exists($imagedir.$_FILES['file']['name']))
			{
                $wpdb->query("UPDATE `wp_product_files` SET `filename` = '".$filename."', `mimetype` = '$mimetype', `width` = '$file_w', `height` = '$file_h' WHERE `id` = '".$file_data[0]['id']."' LIMIT 1");
			}
/*
            if(move_uploaded_file($_FILES['file']['tmp_name'],($filedir.$idhash)))
            {
                $wpdb->query("UPDATE `wp_product_files` SET `filename` = '".$filename."', `mimetype` = '$mimetype', `width` = '$file_w', `height` = '$file_h' WHERE `id` = '".$file_data[0]['id']."' LIMIT 1");
            }
*/
        }

        if(is_numeric($_POST['prodid']))
        {
            if(isset($_POST['image_resize']) && ($_POST['image_resize'] > 0) && ($image === ''))
            {
                $imagesql = "SELECT `image` FROM `wp_product_list` WHERE `id`=".$_POST['prodid']." LIMIT 1";
                $imagedata = $wpdb->get_results($imagesql,ARRAY_A);
                if($imagedata[0]['image'] != '')
                {
                    $imagepath = $imagedir . $imagedata[0]['image'];
                    switch($_POST['image_resize'])
                    {
                        case 0:
                            $height = get_option('product_image_height');
                            $width  = get_option('product_image_width');
                            break;

                        case 1:
                            $height = get_option('product_image_height');
                            $width  = get_option('product_image_width');
                            break;

                        case 2:
                            $height = $_POST['height'];
                            $width  = $_POST['width'];
                            break;
                    }
                    include("image_resize.php");
                }
            }

            if(is_numeric($_POST['prodid']))
            {
                $counter = 0;
                $item_list = '';
                if(count($_POST['category']) > 0)
                {
                    foreach($_POST['category'] as $category_id)
                    {
                        $check_existing = $wpdb->get_var("SELECT `id` FROM `wp_item_category_associations` WHERE `product_id` = ".$id." AND `category_id` = '$category_id' LIMIT 1");
                        if($check_existing == null)
                        {
                            $wpdb->query("INSERT INTO `wp_item_category_associations` ( `id` , `product_id` , `category_id` ) VALUES ('', '".$id."', '".$category_id."');");        
                        }
                        if($counter > 0)
                        {
                            $item_list .= ", ";
                        }
                        $item_list .= "'".$category_id."'";
                        $counter++;
                    }
                }
                else
                {
                    $item_list = "'0'";
                }
                $sql_delete_query = "DELETE FROM `wp_item_category_associations` WHERE `product_id`= '$id' AND `category_id` NOT IN (".$item_list.")";
                $wpdb->query($sql_delete_query); 
            }

            if(isset($_POST['display_frontpage']) && $_POST['display_frontpage'] == "yes")
            {
                $display_frontpage = 1;
            }
            else
            {
                $display_frontpage = 0;
            }

            $visible = '0';
            if (isset($_POST['visible']) && $_POST['visible'] == 'on')
                $visible = '1'; 
            if (isset($_POST['approved']) && $_POST['approved'] == 'on')
            {$approved = '1';} else {$approved = Null;}
            if (isset($_POST['colored']) && $_POST['colored'] == 'on'){$colored = '1';}
            else {$colored = '0';}
            if (isset($_POST['temadnya']) && $_POST['temadnya'] == 'on'){$temadnya = '1';}
            else {$temadnya = '0';}
            if (isset($_POST['not_for_sale']) && $_POST['not_for_sale'] == 'on'){$not_for_sale = '1';}
            else {$not_for_sale = '0';}


            if (isset($_POST['license1']) && $_POST['license1'] == 'on'){$license1 = '1'; }
            else{$license1="0";}
            if (isset($_POST['license2']) && $_POST['license2'] == 'on'){$license2 = '1'; }
            else{$license2="0";}
            if (isset($_POST['license3']) && $_POST['license3'] == 'on'){$license3 = '1'; }
            else{$license3="0";}


            // TODO: take it off to the SQL table
            // License prices
            // Category group 1
            $l1_price_cat1_default = 250;
            $l2_price_cat1_default = 500;
            $l3_price_cat1_default = 2500;
            // Category group 2
            $l1_price_cat2_default = 250;
            $l2_price_cat2_default = 500;
            $l3_price_cat2_default = 2500;
            //UPDATE `wp_product_list` SET l1_price = 200 WHERE id in (SELECT product_id from `wp_item_category_associations` WHERE category_id in (4,14,5,11));

            $category_id = $wpdb->escape($_POST['category']);
            switch($category_id)
            {
                case 4:
                case 14:
                case 5:
                case 11:
                    $l1_price = $l1_price_cat1_default;
                    $l2_price = $l2_price_cat1_default;
                    $l3_price = $l3_price_cat1_default;
                    break;

                case 13:
                case 8:
                case 15:
                case 6:
                    $l1_price = $l1_price_cat2_default;
                    $l2_price = $l2_price_cat2_default;
                    $l3_price = $l3_price_cat2_default;
                    break;

                default:
                    $l1_price = $l1_price_cat1_default;
                    $l2_price = $l2_price_cat1_default;
                    $l3_price = $l3_price_cat1_default;
                    break;
            }

            // unset not available licences
            if (!isset($license1)||$license1=='0'){$l1_price = '0';}
            if (!isset($license2)||$license2=='0'){$l2_price = '0';}
            if (!isset($license3)||$license3=='0'){$l3_price = '0';}

            //        

            if (isset($_POST['brand']) && is_numeric($_POST['brand']))
            {$_brand = $_POST['brand'];}
            else
            {$_brand = $user_brand;}




            if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
            {
                $updatesql = "UPDATE `wp_product_list` SET `name` = '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['title'])))."', `description` = '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."', `additional_description` = '".$wpdb->escape(correct_comma(removeCrLf(htmlspecialchars($_POST['additional_description']))))."', `category` = '".$wpdb->escape($_POST['category'][0])."', `brand` = '".$_brand."', `display_frontpage`='$display_frontpage', `visible`='$visible',`color`='$colored', `not_for_sale`='$not_for_sale', `l1_price`='$l1_price', `l2_price`='$l2_price', `l3_price`='$l3_price'  WHERE `id`='".$_POST['prodid']."' LIMIT 1";
            }
            else
            {
                $updatesql = "UPDATE `wp_product_list` SET `name` = '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['title'])))."', `description` = '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."', `additional_description` = '".$wpdb->escape(correct_comma(removeCrLf(htmlspecialchars($_POST['additional_description']))))."', `category` = '".$wpdb->escape($_POST['category'][0])."', `brand` = '".$_brand."', `display_frontpage`='$display_frontpage', `visible`='$visible', `approved`='$approved', `color`='$colored', `not_for_sale`='$not_for_sale', `l1_price`='$l1_price', `l2_price`='$l2_price', `l3_price`='$l3_price'  WHERE `id`='".$_POST['prodid']."' LIMIT 1";
            }

            $wpdb->query($updatesql);

            // update temadnya
            if ($temadnya == '1') // insert category 777
            {
                $sql_temadnya = "insert into `wp_item_category_associations` (product_id, category_id) values ('".$_POST['prodid']."','777')";
                $wpdb->query($sql_temadnya);
            }
            elseif ($temadnya == '0') // remove category 777
            {
                $sql_temadnya = "delete from `wp_item_category_associations` where product_id=".$_POST['prodid']." and category_id='777'";
                $wpdb->query($sql_temadnya);
            }



            if($image != null)
            {
                $updatesql2 = "UPDATE `wp_product_list` SET `image` = '".$image."' WHERE `id`='".$_POST['prodid']."' LIMIT 1";
                $wpdb->query($updatesql2);
            }
            if(isset($_POST['deleteimage']) && $_POST['deleteimage'] == 1)
            {
                $updatesql2 = "UPDATE `wp_product_list` SET `image` = ''  WHERE `id`='".$_POST['prodid']."' LIMIT 1";
                $wpdb->query($updatesql2);
            }

            $variations_procesor = new nzshpcrt_variations;
            if(isset($_POST['variation_values']) && $_POST['variation_values'] != null)
            {
                $variations_procesor->add_to_existing_product($_POST['prodid'],$_POST['variation_values']); 
            }

            if(isset($_POST['edit_variation_values']) && $_POST['edit_variation_values'] != null)
            {
                $variations_procesor->edit_product_values($_POST['prodid'],$_POST['edit_variation_values']);
            }

            if(isset($_POST['edit_add_variation_values']) && $_POST['edit_add_variation_values'] != null)
            {
                $variations_procesor->edit_add_product_values($_POST['prodid'],$_POST['edit_add_variation_values']);
            }

            echo "<div class='updated'><p align='center'>".TXT_WPSC_PRODUCTHASBEENEDITED."</p></div>";
        }
    }

    if(isset($_GET['deleteid']) && is_numeric($_GET['deleteid']))
    {
        if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
        {
            $deletesql = "UPDATE `wp_product_list` SET  `active` = '0' WHERE `id`='".$_GET['deleteid']."' LIMIT 1";
            $wpdb->query($deletesql);
        }

        if (isset($current_user->wp_capabilities['editor']) && $current_user->wp_capabilities['editor']==1)
        {
            $deletesql = "UPDATE `wp_product_list` SET  `active` = '0' WHERE `id`='".$_GET['deleteid']."' LIMIT 1";
            $wpdb->query($deletesql);
        }

        if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
        {
            $deletesql = "UPDATE `wp_product_list` SET  `active` = '0' WHERE `id`='".$_GET['deleteid']."' LIMIT 1";
            $wpdb->query($deletesql);
        }
    }


    /*
    * Gets the product list, commented to make it stick out more, as it is hard to notice 
    */

    $items_on_page = 15;
    if(isset($_GET['offset']) && is_numeric($_GET['offset']))
    {
        $offset = $_GET['offset'];
    }
    else
    {
        $offset = 0;
    }

    if(current_user_can('publish_posts'))
        $visiblesql = " ";
    else
        $visiblesql = " AND `wp_product_list`.`visible`='1' ";

    $exclude_category_777 = " ";

    if(isset($_GET['catid']) && is_numeric($_GET['catid']))
    {
        // if we are getting items from only one category
        $sql = "SELECT `wp_product_list`.*,`wp_product_list`.`category` AS `category_id` FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$visiblesql.$author_group_sql.$exclude_category_777." AND `wp_product_list`.`category`='".$_GET['catid']."' order by wp_product_list.id DESC LIMIT ".$offset.",".$items_on_page;

        $category_count = $wpdb->get_results("SELECT COUNT(id) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`category`='".$_GET['catid']."'",ARRAY_A);

    }
    else if (isset($_GET['brand']) && is_numeric($_GET['brand']))
        {
            // if we are getting items from only one brand
            $sql = "SELECT `wp_product_list`.*,`wp_product_list`.`category` AS `category_id` FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$visiblesql.$author_group_sql.$exclude_category_777." AND `wp_product_list`.`brand`='".$_GET['brand']."' order by wp_product_list.id DESC LIMIT ".$offset.",".$items_on_page;

            $category_count = $wpdb->get_results("SELECT COUNT(id) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`brand`='".$_GET['brand']."'",ARRAY_A);
        }
        else
        {
            // if not, get everything that is not deleted (denoted by the active column, 1 = present, 0 = deleted, no real deletion because that would screw up the product log)
            $sql = "SELECT `wp_product_list`.*, `wp_product_list`.`category` AS `category_id` FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$visiblesql.$author_group_sql.$exclude_category_777." order by wp_product_list.id DESC LIMIT ".$offset.",".$items_on_page;

            $category_count = $wpdb->get_results("SELECT COUNT(id) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$visiblesql.$author_group_sql.";",ARRAY_A);

    }
    $product_list = $wpdb->get_results($sql,ARRAY_A) ;

    $items_count = $category_count[0]['count'];
?>

<div class="wrap">
    <h2><?php echo TXT_WPSC_DISPLAYPRODUCTS;?></h2>

    <?php
		echo "Фильтры: ";
        echo topcategorylist($offset);
        echo (" или ".al_brandslist());

    ?>

    №:<input type="text" value="000" id='editpicid' style="width:60px;">
    <a href="#" class="button add-new-h2" onclick="var editpicid=document.getElementById('editpicid').value;filleditform(editpicid.replace(/(^\s+)|(\s+$)/g, ''));return false;">Редактировать по номеру</a><br />

    <script language='javascript' type='text/javascript'>
        function conf()
        {
            var check = confirm("<?php echo TXT_WPSC_SURETODELETEPRODUCT;?>");
            if(check)
                {
                return true;
            }
            else
                {
                return false;
            }
        }
        <?php
            if(isset($_POST['prodid']) && is_numeric($_POST['prodid']))
            {
                echo "filleditform(".$_POST['prodid'].");";
            }
            else if(isset ($_GET['product_id']) && is_numeric($_GET['product_id']))
                {
                    echo "filleditform(".$_GET['product_id'].");";
                }
        ?>
    </script>

    <?php
        if($product_list != null){
            $shawn = sizeof($product_list);
        }

        $num = 1;
        if (isset($_GET['brand'])){$_brand = $_GET['brand'];}else{$_brand = '';}
        if (isset($_GET['catid'])){$_category = $_GET['catid'];}else{$_category = '';}

        //pagination links
        $output = "<span id='pagination'><br />";
        $from_num = 0;
        $to_num = 0;
        if(($offset >= $items_on_page) && ($items_count>0))
        {
            // "Previous page" link
            $offset_back = $offset - $items_on_page;
            $output .= "<a href='admin.php?page=wp-shopping-cart/display-items.php&brand=".$_brand."&category=".$_category."&offset=".$offset_back."'><< ".TXT_WPSC_PREV_PAGE."</a><br />";
        }
        if(($offset < $items_count - $items_on_page) && ($items_count>0))
        {
            // "Next page" link
            $offset_forw = $offset + $items_on_page;
            $output .= "<a href='admin.php?page=wp-shopping-cart/display-items.php&brand=".$_brand."&category=".$_category."&offset=".$offset_forw."'>".TXT_WPSC_NEXT_PAGE." >></a>";
        }
        $output .= "</br></div>";

        // main table with two TD (item list & edit form)
        echo "    <table id='productpage'>\n\r";
        echo "      <tr><td valign='top'>\n\r";

        // left table (item list)
        echo "        <table id='itemlist' style='width:120px;'>\n\r";
        echo "          <tr>\n\r";
        // selection message:
        echo "            <td colspan='6' style='padding:4px; text-align:center;'>\n\r";
        $from_num = $offset+1;
        $to_num = $from_num + $items_on_page;
        if($to_num>$items_count){$to_num=$items_count;}
        if($items_count>0)
        {
            $diapazon = " (".$from_num."-".$to_num." из ".$items_count.")";
        }
        else{$diapazon = "<br />нет картинок для показа или у вас нет полномочий для просмотра";}
        echo "Показаны $diapazon";
        echo $output; 
        echo "</td>\n\r";
        echo "</tr>\n\r";
        echo "<tr class='firstrow'>\n\r";

        echo "<td style='width:20px'>";
        echo "#";
        echo "</td>\n\r";

        echo "<td>\n\r";

        echo "Редактир.</td>\n\r";

        echo "<td>";
        echo "Имя";
        echo "</td>\n\r";
/*
        echo "<td>";
        echo "Ред.";
        echo "</td>\n\r";
*/
        echo "</tr>\n\r";

        if($product_list != null)
        {
            foreach($product_list as $product)
            {

                if ($product['category_id']=='666')
                {
                    // рабочий стол
                    $classstyle = " style='background-color:#C9FCED;'";
                }
                elseif ($product['category_id']=='4')
                {
                    // карикатура
                    $classstyle = " style='background-color:#80D6F7;'";
                }
                elseif ($product['category_id']=='5')
                {
                    // cartoon
                    $classstyle = " style='background-color:#F3C1A3;'";
                }
                elseif ($product['category_id']=='11')
                {
                    // разное
                    $classstyle = " style='background-color:#FFC4E1;'";
                }
                else
                {
                    $classstyle = '';
                }

				if (isset($current_user->wp_capabilities['administrator']) &&    $current_user->wp_capabilities['administrator']==1) 
                {
                    $ed = "<br /><a href='#' onclick='filleditform(".$product['id'].");return false;'><img src='../img/edit.jpg' title='редактировать'></a><br /><a href='admin.php?page=wp-shopping-cart/display-items.php&deleteid=".$product['id']."' onclick='return conf();'><img src='../img/trash.gif' title='удалить'></a>";
                }
                else {
                    $ed = "<br><a href='#' onclick='filleditform(".$product['id'].");return false;'><img src='../img/edit.jpg' title='редактировать'></a>";
                }

                echo "<tr>\n\r";
                echo "<td class='tdc2'>";
                echo $num+$offset;
                echo $ed."</td>\n\r";

                echo "<td class='tdc2'$classstyle>";
                $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
                if(file_exists($imagedir.$product['image']))
                {
                    echo "<a href='#' onclick='filleditform(".$product['id'].");return false;'><img src='../wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' width='70' height='70' /></a>";
                }
                else
                {
                    echo "<br /><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' width='70' height='70' /><br />";
                }
                echo "</td>\n\r";

                echo "<td class='tdc2'$classstyle>";
                echo "№ ".$product['id']."<br />";
                echo "<b>".stripslashes($product['name'])."</b><br />[".$category_data[$product['category_id']]."]";
                echo "</td>\n\r";
/*
                echo "            <td class='tdc2'>\n\r";
                echo "<a href='#' onclick='filleditform(".$product['id'].");return false;'><img src='../img/edit.jpg' title='редактировать'></a>";
                echo "            </td>\n\r";
*/
                echo "          </tr>\n\r";
                $num ++;
            }
        }
        echo "        </table>\n\r";
        echo "      </td><td class='secondcol' valign='top' style='padding:4px;background-color:#FFFF99'>\n\r";
    ?>
    <div style='color:#006699'><b>Форма для отправки и редактирования изображения</b> <a href="#" onclick="return showaddform()" class="button add-new-h2" style='color:#000099;'>очистить форму</a></div>
    <?

        echo "        <div id='productform' style='background-color:#FFFF99;'>";
        echo "<form method='POST'  id='editproductformtop' enctype='multipart/form-data' name='editproduct$num'>";
        echo "        <div id='formcontent'>\n\r";
        echo "        </div>\n\r";
        echo "</form>";
        echo "        </div>";

    ?>

    <div id='additem'>
        <form  id='editproductform' method='POST' enctype='multipart/form-data'>
            <table class='additem'>
                <tr>
                    <td class="r">
                        Автор:
                    </td>
                    <td>
                        <?php echo brandslist(); ?> <!-- <input id='approved' type="checkbox" name="approved"> Утверждено. -->
                    </td>
                </tr>
                <tr class='tdfirstcol'>
                    <td class='r'>
                        Укажите файл:
                    </td>
                    <td>
                        <input id='fileupload' type='file' name='file' value='' />
                    </td>
                </tr>
                <tr>
                    <td class='r'>
                        Название рисунка:
                    </td>
                    <td>
                        <input id='picturename' size='40' type='text' name='name' value=''  />
                    </td>
                </tr>
                <tr>
                    <td class='r'>
                        Краткое описание:
                    </td>
                    <td>
                        <textarea id='picturedescription' name='description' cols='50' rows='4'></textarea><br />
                    </td>
                </tr>
                <tr>
                    <td class='r'>
                        Ключевые слова,<br />разделённые<br />запятыми:
                    </td>
                    <td>
                        <textarea id='tags' name='additional_description' cols='50' rows='4'></textarea><br />
                    </td>
                </tr>
                <tr>
                    <td class='r'>
                        Всем видно:
                    </td>
                    <td>
                        <input id='visible' type="checkbox" name="visible" checked="checked"> <span style="color:#999;">Если выключить — не будет видно покупателям</span>
                    </td>
                </tr>
                <tr>
                    <td class='r' style="background-color:#FFFF33;">
                        Цветной рисунок:
                    </td>
                    <td class="lalt">
                        <input id='colored' type="checkbox" name="colored" checked="checked"> <span style="color:#999;">Отключите для ч/б рисунков</span>
                    </td>
                </tr>
                <tr>
                    <td class='r'>
                        Не для продажи:
                    </td>
                    <td>
                        <input id='not_for_sale' type="checkbox" name="not_for_sale"> <span style="color:#999;">Не продаётся, если включено</span>
                    </td>
                </tr>
                <tr>
                    <td class='ralt'>
                        Выберите<br />категорию:
                    </td>
                    <td class='lalt'>
                        <?php echo categorylist(); ?>
                    </td>
                </tr>
                <tr>
                    <td class='r'>
                        Тема дня:
                    </td>
                    <td>
                        <input id='temadnya' type="checkbox" name="temadnya"> <span style="color:#999;">считаю актуальной темой</span>
                    </td>
                </tr>
<?
if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1){
$stle =  "style='display:none;";}
else{$stle = "";}
$stle = "";
?>
                <tr <? echo $stle; ?>>
                    <td class='ralt'>
                        Доступны лицензии:
                    </td>
                    <td class='lalt'>
                        &nbsp;&nbsp;&nbsp;Огр:&nbsp;<input id='license1' type="checkbox" name="license1" checked="checked">&nbsp;&nbsp;&nbsp;Станд:&nbsp;<input id='license2' type="checkbox" name="license2" checked="checked">&nbsp;&nbsp;&nbsp;Расш:&nbsp;<input id='license3' type="checkbox" name="license3" checked="checked"><br />
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td><br />
                        <input type='hidden' name='submit_action' value='add' />
                        <input type="button" name='sendit' style='padding:6px;background-color:#84DF88;' value='Добавить в базу данных' onclick="checkthefields();"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
        echo "      </td></tr>\n\r";
        echo "     </table>\n\r"

    ?>
</div>
<?

    function topcategorylist($offset)
    {
        global $wpdb,$category_data;
        $options = "";
        $values = $wpdb->get_results("SELECT * FROM `wp_product_categories` WHERE `active`='1' ORDER BY `id` ASC",ARRAY_A);
        $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?page=wp-shopping-cart/display-items.php&offset=0";
        $options .= "<option value='$url'>".TXT_WPSC_ALLCATEGORIES."</option>\r\n";
        $selected = '';
        if($values != null)
        {
            foreach($values as $option)
            {
                $category_data[$option['id']] = $option['name'];
                if(isset($_GET['catid']) && $_GET['catid'] == $option['id'])
                {
                    $selected = "selected='selected'";
                }
                $options .= "<option $selected value='$url&catid=".$option['id']."'>".$option['name']."</option>\r\n";
                $selected = "";
            }
        }
        $concat = "<select name='category' onChange='categorylist(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
        return $concat;
    }


    function al_brandslist($current_brand = '')
    {
        global $wpdb;
        global $authors, $user_brand, $current_user;
        $combo_disabled = '';
        if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
            $combo_disabled = "disabled='disabled'";
        if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
            $combo_disabled = '';
        if (isset($current_user->wp_capabilities['editor']) && $current_user->wp_capabilities['editor']==1)
            $combo_disabled = '';
        $options = "";
        $values = $wpdb->get_results("SELECT * FROM `wp_product_brands` WHERE `active`='1' ORDER BY `name` ASC",ARRAY_A);
        $authors = $values;
        $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?page=wp-shopping-cart/display-items.php&offset=0";
        $options .= "<option value='$url'>Выберите автора</option>\r\n";
        $selected = '';
        $who_is_selected_brand = 0;
        if(isset($_GET['brand']) && is_numeric($_GET['brand'])) // we ordered selected user
        {
            $who_is_selected_brand = $_GET['brand'];
        }
        else if (isset($user_brand)) // select logged user
            {
                $who_is_selected_brand = $user_brand;
            }

            foreach($values as $option)
        {
            if($who_is_selected_brand == $option['id'])
            {
                $selected = "selected='selected'";
            }
            $options .= "<option $selected value='$url&brand=".$option['id']."'>".$option['name']."</option>\r\n";
            $selected = "";
        }
        $concat = "<select name='brand' $combo_disabled onChange = 'categorylist(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
        return $concat;
    }

    function al_watermark($path)
    {
        // this script creates a watermarked image from an image file - can be a .jpg .gif or .png file
        // where watermark.gif is a mostly transparent gif image with the watermark - goes in the same directory as this script
        // where this script is named watermark.php
        // call this script with an image tag
        // <img src="watermark.php?path=imagepath"> where path is a relative path such as subdirectory/image.jpg
        //$imagesource =  $_GET['path'];
        $imagesource =  $path;
        $filetype = substr($imagesource,strlen($imagesource)-4,4);
        $filetype = strtolower($filetype);
        if($filetype == ".gif")  $image = @imagecreatefromgif($imagesource);  
        if($filetype == ".jpg")  $image = @imagecreatefromjpeg($imagesource);  
        if($filetype == ".png")  $image = @imagecreatefrompng($imagesource);  
        if (!$image) die(); 
        $watermarkpath = $basepath."/wp-content/plugins/wp-shopping-cart/images/watermark.gif";
        $watermark = @imagecreatefromgif($watermarkpath);
        $imagewidth = imagesx($image);
        $imageheight = imagesy($image);  
        $watermarkwidth =  imagesx($watermark);
        $watermarkheight =  imagesy($watermark);
        $startwidth = (($imagewidth - $watermarkwidth)/2);
        $startheight = (($imageheight - $watermarkheight)/2);
        imagecopy($image, $watermark,  $startwidth, $startheight, 0, 0, $watermarkwidth, $watermarkheight);
        imagejpeg($image);
        imagedestroy($image);
        imagedestroy($watermark);
    }
    function al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality = '100') {

        $img_location = $thatdir.$file;

        // Getting width ([0]) and height ([1]) maybe add options
        $size_bits = getimagesize($img_location);

        // Creating a resource image
        $path = pathinfo($img_location);

        switch(strtolower($path["extension"])){
            case "jpeg":
            case "jpg":
                $img = imagecreatefromjpeg($img_location);
                break;
            case "gif":
                $img = imagecreatefromgif($img_location);
                break;
            case "png":
                $img = imagecreatefrompng($img_location);
                break;
            default:
                break;
        }

        if($size_bits[0] > $chwidth || $size_bits[1] > $chheight) {

            // Resize the image
            $resized = imagecreatetruecolor($chwidth, $chheight);

            $o_width = $size_bits[0];
            $o_height = $size_bits[1];

            // if the image is more wide than high
            if($o_width > $o_height) {
                // landscape image
                $out_width = $o_height;
                $out_height = $o_height;
                $cutoff = round(($o_width - $o_height) / 2);

                $out_left = $cutoff;
                $out_top = 0;
            } else {
                $cutoff = round(($o_height - $o_width) / 2);

                $out_width = $o_width;
                $out_height = $o_width;

                $out_left = 0;
                $out_top = $cutoff;
            }

            // Resampling the image

            imagecopyresampled ($resized, $img, 0, 0, $out_left, $out_top, $chwidth, $chheight, $out_width, $out_height);

            if (is_writable($thatdir.$ifolder)){
                switch(strtolower($path["extension"])){
                    case "jpeg":
                    case "jpg":
                        imagejpeg($resized, $thatdir.$ifolder.'/'.$file, $resample_quality);
                        break;
                    case "gif":
                        imagegif($resized, $thatdir.$ifolder.'/'.$file);
                        break;
                    case "png":
                        imagepng($resized, $thatdir.$ifolder.'/'.$file);
                        break;
                    default:
                        break;
                }
            } else {
                echo "<div class='error'><b>WARNING:</b> Unable to create $ifolder inside $thatdir. <br />";
                echo "Check your permissions.</div><br />";
            }

            imagedestroy($resized);
        } else {
            switch(strtolower($path["extension"])){
                case "jpeg":
                case "jpg":
                    imagejpeg($img, $thatdir.$ifolder.'/'.$file, $resample_quality);
                    break;
                case "gif":
                    imagegif($img, $thatdir.$ifolder.'/'.$file);
                    break;
                case "png":
                    imagepng($img, $thatdir.$ifolder.'/'.$file);
                    break;
                default:
                    break;
            }
        }
    }
    function al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $thumb, $resample_quality = '100') {
        // Default thumbs creation
        // global $gallery_root;
        $gallery_root = '';
        $img_location = $gallery_root.$thatdir.$file;

        // Creating a resource image
        $path = pathinfo($img_location);

        switch(strtolower($path["extension"])){
            case "jpeg":
            case "jpg":
                $img = imagecreatefromjpeg($img_location);
                break;
            case "gif":
                $img = imagecreatefromgif($img_location);
                break;
            case "png":
                $img = imagecreatefrompng($img_location);
                break;
            default:
                break;
        }

        $xratio = $chheight/(imagesx($img));
        $yratio = $chwidth/(imagesy($img));

        if($xratio < 1 || $yratio < 1) {
            if($xratio < $yratio)
                $resized = imagecreatetruecolor($chwidth,floor(imagesy($img)*$xratio));
            else
                $resized = imagecreatetruecolor(floor(imagesx($img)*$yratio), $chheight);

            imagecopyresampled($resized, $img, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($img),imagesy($img));

            if (is_writable($gallery_root.$thatdir.$ifolder)){

                switch(strtolower($path["extension"])){
                    case "jpeg":
                    case "jpg":
                        imagejpeg($resized, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
                        break;
                    case "gif":
                        imagegif($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
                        break;
                    case "png":
                        imagepng($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
                        break;
                    default:
                        break;
                }
            }
            else {
                echo "<div class='error'><b>WARNING:</b> Unable to create images inside $ifolder. <br />";
                echo "Check your permissions.</div>";
            }

            imagedestroy($resized);

        } else {
            switch(strtolower($path["extension"])){
                case "jpeg":
                case "jpg":
                    imagejpeg($img, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
                    break;
                case "gif":
                    imagegif($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
                    break;
                case "png":
                    imagepng($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
                    break;
                default:
                    break;
            }
        }
    }
    function wtrmark($sourcefile, $watermarkfile) {
        #
        # $sourcefile = Filename of the picture to be watermarked.
        # $watermarkfile = Filename of the 24-bit PNG watermark file.
        #

        $basepath = str_replace("/wp-admin", "" , getcwd()); 
        $basepath = str_replace("\\wp-admin", "" , $basepath);

        $logopath = $basepath."/img/cb-logo-300.png";
        $watermarkfile = $basepath."/img/watermark.png";

        $logofile_id = imagecreatefrompng($logopath);

        $watermarkfile_id = imagecreatefrompng($watermarkfile);

        imageAlphaBlending($watermarkfile_id, true);
        imageSaveAlpha($watermarkfile_id, true);

        imageAlphaBlending($logofile_id, true);
        imageSaveAlpha($logofile_id, true);


        $fileType = strtolower(substr($sourcefile, strlen($sourcefile)-3));

        switch($fileType) {
            case('gif'):
                $sourcefile_id = imagecreatefromgif($sourcefile);
                break;

            case('png'):
                $sourcefile_id = imagecreatefrompng($sourcefile);
                break;

            default:
                $sourcefile_id = imagecreatefromjpeg($sourcefile);
        }
        imageAlphaBlending($sourcefile_id, true);
        imageSaveAlpha($sourcefile_id, true);


        //Get the sizes of both pix 
        $sourcefile_width=imageSX($sourcefile_id);
        $sourcefile_height=imageSY($sourcefile_id);
        $watermarkfile_width=imageSX($watermarkfile_id);
        $watermarkfile_height=imageSY($watermarkfile_id);
        $logo_width=imageSX($logofile_id);
        $logo_height=imageSY($logofile_id);

        $dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
        $dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 );
        $dest_x_logo = $sourcefile_width - $logo_width - 4;
        $dest_y_logo = $sourcefile_height - $logo_height - 4;


        // if a gif, we have to upsample it to a truecolor image
        if($fileType == 'gif') {
            // create an empty truecolor container
            $tempimage = imagecreatetruecolor($sourcefile_width,$sourcefile_height);

            // copy the 8-bit gif into the truecolor image
            imagecopy($tempimage, $sourcefile_id, 0, 0, 0, 0,
            $sourcefile_width, $sourcefile_height);

            // copy the source_id int
            $sourcefile_id = $tempimage;
        }

        //imagecopy($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0,
        //                   $watermarkfile_width, $watermarkfile_height);


        $opacity = 10;
        $opacity_logo = 60;

        ImageCopyMerge($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height, $opacity);

        ImageCopyMerge($sourcefile_id, $logofile_id, $dest_x_logo, $dest_y_logo, 0, 0, $logo_width, $logo_height, $opacity_logo);



        //Create a jpeg out of the modified picture 
        switch($fileType) {

            // remember we don't need gif any more, so we use only png or jpeg.
            // See the upsaple code immediately above to see how we handle gifs
            case('png'):
                //header("Content-type: image/png");
                imagepng ($sourcefile_id,$sourcefile);
                break;

            default:
                //header("Content-type: image/jpg"); 
                imagejpeg ($sourcefile_id,$sourcefile);
        }

        imagedestroy($sourcefile_id);
        imagedestroy($watermarkfile_id);
        imagedestroy($logofile_id);

    }

    wp_enqueue_script('jquery');

    if (current_user_can('manage_options') && isset($_POST['edid']) && is_numeric($_POST['edid']))
    {
        $_edid = $_POST['edid'];
    }
    elseif (current_user_can('manage_options') && isset($_GET['edid']) && is_numeric($_GET['edid']))
    {
        $_edid = $_GET['edid'];
    }
    else
    {
        $_edid = 0;
    }

    function correct_comma($string)
    {
        $string = str_replace(",", ", " , $string); 
        $string = str_replace(",  ", ", " , $string); 
        return $string;
    }

?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var edid = <? echo $_edid; ?>;
        if (edid!=0)
            {
            filleditform(edid);
        }
    });
</script>