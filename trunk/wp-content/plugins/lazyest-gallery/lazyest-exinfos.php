<?php

include_once('exifer/exif.php');

// Takeing info about image...
$imgPath = $gallery_root.$currentdir.$slide;
$file_infos = pathinfo($imgPath);

echo '<div class="imageData"><b>Image data</b><br />';

// ...and check if it's a valid jpg for exif data parsing
if ($file_infos["extension"] == "jpeg" || $file_infos["extension"] == "jpg") {

	//	Let's build exif array data
	$verbose = 0;
	$exif_array = read_exif_data_raw($imgPath, $verbose);

	echo	'<table summary="displaying" border="0" class="imageDataTable" cellpadding="0" cellspacing="0">';

	if(get_option('lg_exif_print_error') == "TRUE") {
			echo '<tr><th scope="row">Errors: </th><td>';
			echo $exif_array['Errors'];
			echo '</td></tr>';
	}
	if(get_option('lg_exif_valid_jpeg') == "TRUE") {
			echo '<tr><th scope="row">Valid Jpeg: </th><td>';
			echo $exif_array['ValidJpeg'];
			echo '</td></tr>';
	}
	if(get_option('lg_exif_valid_jfif_data') == "TRUE") {
			echo	'<tr><th scope="row">Valid JFIF Data: </th><td>';
			echo $exif_array['ValidJFIFData'];
			echo '</td></tr>';
	}
	if(get_option('lg_exif_jfif_size') == "TRUE" ||
		get_option('lg_exif_jfif_identifier') == "TRUE" ||
		get_option('lg_exif_jfif_extension_code') == "TRUE" ||
		get_option('lg_exif_jfif_data') == "TRUE") {
		echo '<tr><th scope="row">JFIF Data: </th><td>';
		echo '<table summary="Image SubData" border="0" cellpadding="0" cellspacing="0" class="imageSubTable">';
		if(get_option('lg_exif_jfif_size') == "TRUE") {
			echo '<tr><th scope="row">Size: </th><td>';
			echo $exif_array['JFIF']['Size'];
			echo '</td></tr>';
		}
		if(get_option('lg_exif_jfif_identifier') == "TRUE") {
			echo '<tr><th scope="row">Identifier: </th><td>';
			echo $exif_array['JFIF']['Identifier'];
			echo '</td></tr>';
		}
		if(get_option('lg_exif_jfif_extension_code') == "TRUE") {
			echo '<tr><th scope="row">Extension Code: </th><td>';
			echo $exif_array['JFIF']['ExtensionCode'];
			echo '</td></tr>';
		}
		if(get_option('lg_exif_jfif_data') == "TRUE") {
			echo '<tr><th scope="row">Data: </th><td>'
			.$exif_array['JFIF']['Data'].'</td></tr>';
		}
		echo '</table></td></tr>';
	}
	if(get_option('lg_exif_valid_exif_data') == "TRUE") {
		echo '<tr><th scope="row">Valid ExIF Data: </th><td>';
		echo $exif_array['ValidEXIFData'];
		echo '</td></tr>';
	}
	if(get_option('lg_exif_app1_size') == "TRUE") {
		echo '<tr><th scope="row">APP 1 Size: </th><td>';
		echo $exif_array['APP1Size'];
		echo '</td></tr>';
	}
	if(get_option('lg_exif_endien') == "TRUE") {
		echo	'<tr><th scope="row">Endien: </th><td>';
		echo $exif_array['Endien'];
		echo '</td></tr>';
	}
	if(get_option('lg_exif_ifd0_num_tags') == "TRUE") {
		echo	'<tr><th scope="row">IFD0 Number Tags: </th><td>';
		echo $exif_array['IFD0NumTags'];
		echo '</td></tr>';
	}
	if(get_option('lg_exif_ifd0_orientation') == "TRUE" ||
		get_option('lg_exif_ifd0_x_res') == "TRUE" ||
		get_option('lg_exif_ifd0_y_res') == "TRUE" ||
		get_option('lg_exif_ifd0_res_unit') == "TRUE" ||
		get_option('lg_exif_ifd0_software') == "TRUE" ||
		get_option('lg_exif_ifd0_time') == "TRUE" ||
		get_option('lg_exif_ifd0_offset') == "TRUE") {
		echo '<tr><th scope="row">IFD0 Data: </th><td>';
		echo '<table summary="Image SubData" border="0" cellpadding="0" cellspacing="0" class="imageSubTable">';
		if(get_option('lg_exif_ifd0_orientation') == "TRUE") {
			echo '<tr><th scope="row">Orientation: </th><td>'
			.$exif_array['IFD0']['Orientation'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd0_x_res') == "TRUE") {
			echo '<tr><th scope="row">X Resolution: </th><td>'
			.$exif_array['IFD0']['xResolution'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd0_y_res') == "TRUE") {
			echo '<tr><th scope="row">Y Resolution: </th><td>'
			.$exif_array['IFD0']['yResolution'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd0_res_unit') == "TRUE") {
			echo '<tr><th scope="row">Resolution Unit: </th><td>'
			.$exif_array['IFD0']['ResolutionUnit'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd0_software') == "TRUE") {
			echo '<tr><th scope="row">Software: </th><td>'
			.$exif_array['IFD0']['Software'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd0_time') == "TRUE") {
			echo '<tr><th scope="row">Date Time: </th><td>'
			.$exif_array['IFD0']['DateTime'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd0_offset') == "TRUE") {
			echo '<tr><th scope="row">Exif Offset: </th><td>'
			.$exif_array['IFD0']['ExifOffset'].'</td></tr>';
		}
		echo '</table></td></tr>';
	}
	if(get_option('lg_exif_ifd1_main_offset') == "TRUE") {
		echo	'<tr><th scope="row">IFD1 Offset:</th><td>';
		echo $exif_array['IFD1Offset'];
		echo '</td></tr>';
	}
	if(get_option('lg_exif_sub_ifd_num_tags') == "TRUE") {
		echo	'<tr><th scope="row">Sub IFD Number Tags: </th><td>';
		echo $exif_array['SubIFDNumTags'];
		echo '</td></tr>';
	}
	if(get_option('lg_exif_sub_ifd_color_space') == "TRUE" ||
		get_option('lg_exif_sub_ifd_width') == "TRUE" ||
		get_option('lg_exif_sub_ifd_height') == "TRUE") {
		echo '<tr><th scope="row">SubIFD Data: </th><td>';
		echo '<table summary="Image SubData" border="0" cellpadding="0" cellspacing="0" class="imageSubTable">';
		if(get_option('lg_exif_sub_ifd_color_space') == "TRUE") {
			echo '<tr><th scope="row">Color Space: </th><td>'
			.$exif_array['SubIFD']['ColorSpace'].'</td></tr>';
		}
		if(get_option('lg_exif_sub_ifd_width') == "TRUE") {
			echo '<tr><th scope="row">Exif Image Width: </th><td>'
			.$exif_array['SubIFD']['ExifImageWidth'].'</td></tr>';
		}
		if(get_option('lg_exif_sub_ifd_height') == "TRUE") {
			echo '<tr><th scope="row">Exif Image Height: </th><td>'
			.$exif_array['SubIFD']['ExifImageHeight'].'</td></tr>';
		}
		echo '</table></td></tr>';
	}
	if(get_option('lg_exif_ifd1_num_tags') == "TRUE") {
		echo '<tr><th scope="row">IFD1 Num Tags: </th><td>';
		echo $exif_array['IFD1NumTags'];
		echo '</td></tr>';
	}
	if(get_option('lg_exif_ifd1_compression') == "TRUE" ||
		get_option('lg_exif_ifd1_x_res') == "TRUE" ||
		get_option('lg_exif_ifd1_y_res') == "TRUE" ||
		get_option('lg_exif_ifd1_res_unit') == "TRUE" ||
		get_option('lg_exif_ifd1_jpeg_if_offset') == "TRUE" ||
		get_option('lg_exif_ifd1_jpeg_if_byte_count') == "TRUE") {
		echo '<tr><th scope="row">IFD0 Data: </th><td>';
		echo '<table summary="Image SubData" border="0" cellpadding="0" cellspacing="0" class="imageSubTable">';
		if(get_option('lg_exif_ifd1_compression') == "TRUE") {
			echo '<tr><th scope="row">Compression: </th><td>'
			.$exif_array['IFD1']['Compression'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd1_x_res') == "TRUE") {
			echo '<tr><th scope="row">X Resolution: </th><td>'
			.$exif_array['IFD1']['xResolution'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd1_y_res') == "TRUE") {
			echo '<tr><th scope="row">Y Resolution: </th><td>'
			.$exif_array['IFD1']['yResolution'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd1_res_unit') == "TRUE") {
			echo '<tr><th scope="row">Resolution Unit: </th><td>'
			.$exif_array['IFD1']['ResolutionUnit'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd1_jpeg_if_offset') == "TRUE") {
			echo '<tr><th scope="row">Jpeg IF Offset: </th><td>'
			.$exif_array['IFD1']['JpegIFOffset'].'</td></tr>';
		}
		if(get_option('lg_exif_ifd1_jpeg_if_byte_count') == "TRUE") {
			echo '<tr><th scope="row">Jpeg IF Byte Count: </th><td>'
			.$exif_array['IFD1']['JpegIFByteCount'].'</td></tr>';
		}
		echo '</table></td></tr>';
	}
	echo '</table>';
} // closes ExIF Table data "if"
// otherwise print the size only
else {
	echo '<div class="quote"><table summary="Image Data" border="0" cellpadding="0" cellspacing="0" style="padding-left:10px;">
				<th>.:Field</th>
				<th style="padding-left:10px;">.:Data</th>
				<tr><td><i>Image Size:</i></td><td style="padding-left:10px;">'.$xsize.' x '.$ysize.'</td></tr>
			</table></div>';
}
echo '</div>';	// Closes the imgdata div class

?>