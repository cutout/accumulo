<?php
/**
 * Add some form of input to an admin page
 *
 * @param string $var the name of the variable to be saved
 * @param string $type the type of input to display
 * @param string $description a short peice of text to display with the input
 * @param string $value the default value of the input element
 * @param string $selected the currently select value (used in select/ image select types)
 * @param string $onchange a javascript function to call when somehting is changed
 */
function bm_input ($var, $type, $description = '', $value = NULL, $selected = '', $onchange = '') {

	// default value
	$extra = '';
	
	// ------------------------
	// add a form input control
	// ------------------------
		
 	echo "\n";
		
	switch ($type){
		
		// normal text input
	    case 'text':
	    case 'int':
	    case 'integer':
			
	 		echo '<input name="' . $var . '" id="' . $var . '" type="text" style="width: 70%;" class="code" value="' . $value . '" onchange="' . $onchange . '"/>';
			if ($description != "") { 
				echo '<p style="font-size:0.9em; color:#999; margin:0;">' . $description . '</p>';
			}
			
			break;

		// a hidden field
	    case 'hidden':
			
	 		echo '<input name="' . $var . '" id="' . $var . '" type="hidden" style="display:none;" value="' . $value . '"/>';
			
			break;
			
		// submit button
		case 'submit':
			
	 		echo '<span class="submit"><input name="' . $var . '" type="submit" value="' . $value . '" class="button-primary"/></span>';
			
			break;
		
		//
		case 'option':
			
			if ($selected == $value) {
				$extra = 'selected="true"';
			}
			
			echo '<option value="' . $value . '" ' . $extra . '>' . $description . '</option>';
			
		    break;
		   
		// radio button
  		case 'radio':
			
			if ($value == 1 || $value == TRUE) {
				$extra = 'checked="true"';
			}
			
  			echo '<label for="' . $var . '"><input name="' . $var . '" id="' . $var . '" type="radio" value="' . $value . '" ' . $extra . '/>' . $description . '</label><br/>';
  			
  			break;
			
		// checkbox
		case 'checkbox':
			
			if ($value == 1 || $value == TRUE) {
				$extra = 'checked="true"';
			}
			
  			echo '<label><input name="' . $var . '" id="' . $var . '" type="checkbox" value="' . $value . '" ' . $extra . '/>' . $description . '</label><br/>';
			
  			break;
			
		// big text box
		case 'textarea':
			
		    echo '<textarea name="' . $var . '" id="' . $var . '" style="width: 90%; height: 10em;" class="code">' . $value . '</textarea>';
			
		    break;
			
		// new colour picker
		case 'color':
		case 'colour':
		
			if($value == '' || $value == NULL) {
				$value = '#333333';
			}
?>
		<input type="text" id="colorwell<?php echo $var; ?>" class="bm_colorInput" name="<?php echo $var; ?>" value="<?php echo $value; ?>" />
<?php
			break;
	}

}


/**
 *
 * @param <type> $var
 * @param <type> $possible
 * @param <type> $selected
 */
function bm_dragDroplist ($var, $possible, $selected) {

	$selected = (array) $selected;
?>
	<input id="<?php echo $var; ?>" name="<?php echo $var; ?>" value="<?php echo implode($selected, ','); ?>" type="hidden" class="hidden" />
	<div class="connectedSortContainer" style="width:200px; margin-right:10px;">
		<strong>Active</strong>
		<ul id="sortable_<?php echo $var; ?>_1" class="connectedSort connectedSort_<?php echo $var; ?> clearfix">
<?php
	foreach ($selected as $s) {
		$i = array();
		foreach ($possible as $p) {
			if ($p[0] == $s) {
				$i = $p;
			}
		}

		if (!empty($i)) {
			echo '<li cat="' . $i[0] . '">' . $i[1] . '</li>';
		}
	}
?>
		</ul>
	</div>

	<div class="connectedSortContainer">
		<strong>Inactive</strong>
		<ul id="sortable_<?php echo $var; ?>_2" class="connectedSort connectedSort_<?php echo $var; ?> connected_big clearfix">
<?php
	foreach ($possible as $p) {
		if (!in_array($p[0], $selected)) {
			echo '<li cat="' . $p[0] . '">' . $p[1] . '</li>';
		}
	}
?>
		</ul>
	</div>
<?php
}


/**
 * add a select box to the options page
 *
 * @param string $var the name of the variable to be saved
 * @param array $arrValues an array of values to populate the list with
 * @param string $selected the default value of the input element
 * @param string $label text label to place next to select input
 */
function bm_select ($var, $arrValues = array(), $selected = '', $label = '', $size = null) {

	$arrValues = (array) $arrValues;

	if ($label != '') {
		echo '<label for="' . $var . '">' . $label . '</label>';
	}
	
	$class = 'selectbox';
	
	if (count ($arrValues) > 0) {
		$sizeVal = '';
		if (!empty($size)) {
			$size = ' size="' . $size . '"';
			$class = 'selectlist';
		}

		echo '<select name="' . $var . '" id="' . $var . '" ' . $size . ' class="' . $class . '">';

		foreach ($arrValues as $arr) {
		
			if(!isset($arr[1])) {
				$arr[1] = $arr[0];
			}
			
			$extra = '';
			if ($arr[0] == $selected) {
				$extra = ' selected="true"';
			}

			echo '<option value="' . $arr[0] . '"' . $extra . '>' . $arr[1] . '</option>';

		}
		
		echo '</select>';
	} else {
		echo 'no array values specified for ' . $var;
	}
	
}


/**
 * add a multi select box to the options page
 *
 * @param string $var the name of the variable to be saved
 * @param array $arrValues an array of values to populate the list with
 * @param string $selected the default value of the input element
 * @param string $label text label to place next to select input
 */
function bm_multiselect ($var, $arrValues = array(), $selected = '', $label = '') {

	$arrValues = (array) $arrValues;
	
	if ($label != '') {
		echo '<label for="' . $var . '">' . $label . '</label>';
	}
	
	if (count($arrValues) > 0) {
		echo '<select name="' . $var . '[]" id="' . $var . '" size="7" multiple="true" style="height:150px;">';

		foreach ($arrValues as $arr) {
			
			$extra = "";
			if (is_array($selected)) {
				if (in_array($arr[0], $selected)) {
					$extra = ' selected="true"';
				}
			}
			
			echo '<option value="' . $arr[0] . '"' . $extra . '>' . $arr[1] . '</option>';

		}
		
		echo '</select>';
		echo '<p style="font-size:0.9em; color:#999; margin:0;">' . __('NB: To select more than 1 option press and hold "CTRL" (Apple Key/ Command on a Mac) when selecting the desired item', BM_THEMENAME) . '</p>';
	} else {
		echo 'no array values specified for ' . $var;
	}
	
}


/**
 * add an image select carousel to the options page
 *
 * @param string $var the name of the variable to be saved
 * @param array $arrValues an array of values to populate the list with
 * @param string $selected the default value of the input element
 * @param string $label text label to place next to select input
 */
function bm_imageSelect ($var, $arrValues = array(), $selected = '', $label = '') {

	$arrValues = (array) $arrValues;

	if ($label != '') {
		echo '<label for="' . $var . '">' . $label . '</label>';
	}
	
	$bmWidth = 132 * count($arrValues);
	
	echo '<div id="next_' . $var . '" class="bm_carousel_button">&laquo;</div>';
	echo '<div id="carousel_' . $var . '" class="bm_carousel">';
	echo '<ul style="width:' . $bmWidth . 'px">';

	foreach ($arrValues as $arr) {
		
		$extra = '';
		if (is_array($selected)) {
			if (in_array($arr[0], $selected)) {
				$extra = ' selected="true"';
			}
		}
?>
		<li class="imageSelect">
			<label>
				<img src="<?php echo $arr[2]; ?>" width="100" height="100" alt="<?php echo $arr[1]?>" />		
				<input name="<?php echo $var; ?>" type="radio" value="<?php echo $arr[0] ?>" <?php echo $extra ?>">
					<?php echo $arr[1]?>
				</input>
			</label>
		</li>
<?php
	}
	
	echo '</ul>';
	echo '</div>';
	echo '<div id="last_' . $var . '" class="bm_carousel_button">&raquo;</div>';
	
}


/**
 * Select a font from a list of allowed fonts
 *
 * @param <type> $var
 * @param <type> $arrValues
 * @param <type> $selected
 * @param <type> $label
 */
function bm_selectFont ($var, $selected = '') {
	
	// font list
	$fonts = bm_fontSettings ();
	foreach ($fonts as $k => $f) {
		if ($f['type'] == FONT_GOOGLE) {
			$f['name'] = $f['name'] . ' (Google Font)';
		}
		$arrValues[] = array ($k, $f['name']);
	}
	
	echo '<div class="font_controls">';
	bm_select ($var, $arrValues, $selected, '', 5);
	echo '</div>';
	
	// preview font
?>
<div id="fontFrame_<?php echo $var; ?>" class="bm_fontFrame">
	<h1>The Quick Brown Fox Jumped Over the Lazy Dog</h1>
	<p>The Quick Brown Fox Jumped Over the Lazy Dog</h1>
</div>
<?php

}


/**
 * Add a simple image upload form to the website
 *
 * @param <type> $var
 * @param <type> $value
 * @param <type> $width
 * @param <type> $height
 */
function bm_uploadImage ($var, $value = '', $width = 0, $height = 0) {

    echo '<p><input type="file" name="file_' . $var.'"></input></p>';
	echo '<p><input class="code" name="' . $var . '" id="fileValue_' . $var . '" value="' . $value . '" style="clear:both; width:70%;" /> <a href="#" onclick="javascript:jQuery(\'#fileValue_' . $var . '\').val(\'\'); return false;" class="button">' . __('clear', BM_THEMENAME) . ' </a></p>';
	
	if ((int) $width <= 0) {
		$width = 300;
	}
	if ((int) $height <= 0) {
		$height = 75;
	}

	if ($value != '') {
		$imagePath = BM_BLOGPATH . '/tools/timthumb.php?w=' . $width . '&amp;h=' . $height . '&amp;src=' . urlencode(bm_muImageUploadPath($value));
		echo '<img src="' . $imagePath . '" alt="" style="margin-top:10px;" />';
	}

}


/**
 * add a table row and add a label
 * for use in the administration screens
 *
 * @param <type> $title
 * @param <type> $var
 * @param <type> $class
 */
function bm_th ($title, $var, $class = '') {
?>
	<tr valign="top" class="<?php echo $class; ?>">
		<th scope="row">
			<label for="<?php echo $var; ?>"><?php echo $title;?>:</label>
		</th>
		<td>
<?php
}


/**
 * close table row and add a description if specified
 *
 * @param <type> $description 
 */
function bm_cth ($description = '') {

	$class = '';
	if ($description != '') {
		$class = 'class="bm_tdsmall"';
	}
	
?>
		</td>
		<td <?php echo $class; ?>>
<?php
	
	if ($description != '') { 
		echo '<span class="setting-description">' . $description . '</span>';
	}
?>	
		</td>
	</tr>
<?php

}


/**
 *
 * @param <type> $fields
 * @param <type> $values
 * @param <type> $type
 * @return <type>
 */
function bm_writeFields ($fields = null, $values = array(), $type = 1) {
	
	if ($fields == null) {
		return FALSE;
	}	
?>
			<table width="100%" cellspacing="2" cellpadding="5"  class="editform form-table">
<?php
	foreach ($fields as $f) {
		
		if (isset($f['type']) && isset($f['var'])) {

			$value = '';

			if (isset ($values[$f['var']])) {
				$value = $values[$f['var']];
			}
	
			// start the row
			bm_th ($f['name'], $f['var'], $f['type']);
			
			switch ($f['type']) {
			
				case 'select':
					bm_select ($f['var'], $f['values'], $value, '');
					break;
					
				case 'multiSelect':
					bm_multiSelect ($f['var'], $f['values'], $value, '');
					break;

				case 'drag_drop_list':
				case 'drag drop list':
					bm_dragDroplist ($f['var'], $f['values'], $value, '');
					break;

				case 'imageSelect':
					bm_imageSelect ($f['var'], $f['values'], $value, '');
					break;
					
				case 'upload':
				case 'uploadImage':
					bm_uploadImage ($f['var'], $values[$f['var'] . 'Raw'], $f['previewWidth'], $f['previewHeight']);
					break;

				case 'font':
					bm_selectFont ($f['var'], $value);
					
				case 'hidden':
				case 'checkbox':
				case 'text':
				default:
					bm_input ($f['var'], $f['type'], '', $value);
					break;
			}
			
			register_setting ('bm_options', $f['var'], '');
			
			// end the row. Different styles for different places
			switch ($type) {
			
				case 1:
					bm_cth($f['description']);
					break;
					
				case 2:
					echo '<br /><span class="setting-description">' . $f['description'] . '</span>';
					bm_cth();
					break;
					
			}
			
		}

	}
?>
			</table>
<?php
}
?>