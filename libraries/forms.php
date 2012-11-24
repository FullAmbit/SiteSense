<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class customFormHandler extends formHandler{
	function __construct($fields, $dataName, $formName, $data=false, $admin=false, $action=NULL, $customFormFields=NULL){
		if($data !== false){
			$this->enableAJAX = ($data->currentPage == 'ajax') ? true : false;
			if($action == NULL){
				$this->action = $data->linkRoot . implode('/', array_filter($data->action));
			} else {
				$this->action = $data->linkRoot.$action;
			}
		}
		$this->formPrefix = $dataName . '_';
		$this->fromForm = $dataName;
		$this->fields = $fields;
		$this->submitTitle = 'Submit';
		$this->caption = $formName;
		$this->customFormFields=$customFormFields;
		//And some defaults for the form items.
		$defaults = array(
			'params' => array(),
			'error' => false,
			'required' => false,
			'class' => '',
			'value' => '',
			'compareFailed' => false,
			'type' => false,
			'errorList' => array()
		);
		foreach($this->fields as &$field){
			$field = array_merge($defaults, $field);
			if(!isset($field['params']['type'])){
				$field['params']['type'] = '';
			}elseif($field['params']['type']==='file'){
				$this->enctype='multipart/form-data';
			}
		}
	}
}
class formHandler {
	public
		$action,
		$enctype = 'application/x-www-form-urlencoded',
		$formPrefix,
		$caption,
		$customFormFields=array(),
		$submitTitle,
		$method = 'post',
		$fromForm,
		$fields = array(),
		$sendArray=array(),
		$error=false,
		$errorText='',
		$extraMarkup='',
		$randomName = '',
		$contentAfter,
		$enableAJAX,
		$filetypes;
	function __construct($dataName,$data=false,$admin=false) {
		
		//Just some defaults to potentially save time in the formArray files.
		if($data !== false){
			$this->action = $data->linkRoot . implode('/', array_filter($data->action));
			$this->enableAJAX = ($data->currentPage == 'ajax') ? true : false;
		}
		$this->randomName = substr(md5(rand() * time()),0,10);
		$this->formPrefix = $dataName . '_';
		$this->fromForm = $dataName;

        if($admin) {
            $moduleName=array_search($data->action[1],$data->output['moduleShortName']);
            $target='modules/'.$moduleName.'/admin/forms/'.$moduleName.'.admin.form.'.$dataName.'.php';
        } else {
            $target='modules/'.$data->action[0].'/forms/'.$data->action[0].'.form.'.$dataName.'.php';
        }
		require_once($target);

		//And some defaults for the form items.
		$defaults = array(
			'params' => array(),
			'error' => false,
			'required' => false,
			'class' => '',
			'value' => '',
			'compareFailed' => false,
			'errorList' => array()
		);
        $hiddenFields=array();
        foreach($this->fields as $field => $fieldData){
            if(isset($fieldData['tag'])&&$fieldData['tag']==='span'){
                $hiddenFields[$field.'_hidden']= array(
                    'tag' => 'input',
                    'params' => array(
                        'type' => 'hidden'
                    ),
                    'value' => $fieldData['value']
                );
            }
			if(isset($fieldData['params']['type'])&&$fieldData['params']['type']==='file'){
				$this->enctype='multipart/form-data';
			}
        }
        $this->fields=array_merge($this->fields,$hiddenFields);
		foreach($this->fields as &$field){
			$field = array_merge($defaults, $field);
			if(!isset($field['params']['type'])){
				$field['params']['type'] = '';
			}
		}
	}
	function validateFromPost($data = FALSE) {
        $validData=true;
		if (isset($_POST['fromForm'])) {
			foreach ($this->fields as $formKey => &$formField) {
                $formField['errorList']=array();
                if($formField['params']['type'] == 'file'){
                    if(array_key_exists($this->formPrefix.$formKey, $_FILES) && $_FILES[$this->formPrefix.$formKey]["name"] !== ""){
                        $value = &$_FILES[$this->formPrefix.$formKey];
                        // Regular Old File Upload Not An Image
                        if(isset($formField['file']) && is_array($formField['file']))
                        {
                            $info = $formField['file'];
                            $fileSize = $value['size'];
                            $fileName = $value['name'];
                            $fileType = $value['type'];
                            $errorCode = $value['error'];
                            $extension = substr($fileName, strrpos($fileName, '.') + 1);
                            $basename = substr($fileName, 0, strrpos($fileName, '.'));

                            // Error?
                            if($errorCode > 0)
                            {
                                $formField['errorList'][] = 'PHP File Upload Error: '.$errorCode;
                                continue;
                            }

                            // Mandating Extension
                            if(isset($info['mandateExt']) && $info['mandateExt'] !== FALSE && !in_array($extension,$info['mandateExt']))
                            {
                                $formField['error'] = true;
                                $validData = false;
                                $formField['errorList'][] = 'Please provide a file with the extension '.implode(',',$info['mandateExt']).'.';
                                //continue;
                            }
                            // Mandating File-Type
                            if(isset($info['mandateType']) && $fileType !== '' && $info['mandateType'] !== FALSE && !in_array($fileType,$info['mandateType']))
                            {
                                $formField['error'] = true;
                                $validData = false;
                                $formField['errorList'][] = 'Please provide a file of one of the following types: <br />'.implode(',',$info['mandateType']);
                                //continue;
                            }
                            // Check File-Size
                            if(isset($info['mandateSize']) && $info['mandateSize'] !== FALSE && $info['mandateSize'] < $fileSize)
                            {
                                $formField['error'] = true;
                                $validData = false;
                                $formField['errorList'][] = 'Your file can be no larger than '.$info['mandateSize'].' bytes';
                            }
                            // We Found Errors? Bail.
                            if(!$validData)
                            {
                                continue;
                            }
                            // Let's Move It To The Proper Path
                            if(isset($info['path']))
                            {
                                $name = (isset($info['customName'])) ? $info['customName'] : $fileName;
                                $path = rtrim($info['path'],'/').'/'.$name.'.mp3';
                                move_uploaded_file($value['tmp_name'],rtrim($info['path'],'/').'/'.$name.'.mp3');
                            }
                        }
                        // Image Upload
                        if(isset($formField['images']) &&is_array($formField['images'])){
                            switch($value['type']){
                                case 'image/jpeg': case 'image/pjpeg':
                                    $type = 'jpeg';
                                    break;
                                case 'image/png' : case 'image/x-png':
                                    //system("pngcrush -brute -l 9 ".$value['tmp_name']." ".$value['tmp_name']."_compressed",$status);
                                //	unlink($value['tmp_name']);
                                    //rename($value['tmp_name']."_compressed",$value['tmp_name']);
                                    $type = 'png';
                                    break;
                                case 'image/gif':
                                    $type = 'gif';
                                    break;
                                default:
                                    $validData = false;
                                    $formField['error'] = true;
                                    continue;
                                    break;
                            }
                            $this->filetypes[$formKey] = ($type == 'jpeg') ? 'jpg' : $type;
                            $function = 'imagecreatefrom' . $type;
                            $image = $function($value['tmp_name']);
                            foreach($formField['images'] as &$info){
                                // Do We Require A Specific Type?
                                if(isset($info['mandateType']))
                                {
                                    if(!in_array($type,$info['mandateType']))
                                    {
                                        $formField['error'] = true;
                                        $validData = false;
                                        $formField['errorList'][] = 'Please provide a '.implode(' or ',$info['mandateType']).' image.';
                                        continue;
                                    }
                                }
                                // Face Detection? //
                                if(isset($info['faceDetect']) && $info['faceDetect'] === TRUE)
                                {

                                    common_include('plugins/faceDetection.php');

                                    $faceDetect = new Face_Detector("plugins/detection.dat");

                                    if($faceDetect->face_detect($image) !== FALSE)
                                    {
                                        $image = $faceDetect->returnCropped();
                                    }
                                }
                                // Copy Over Image To Another Variable To Use
                                $newImage = $image;
                                // Do we require a specific size?
                                if(isset($info['mandateSize']))
                                {
                                    $currentWidth = imagesx($image);
                                    $currentHeight = imagesy($image);
                                    if(($currentWidth !== $info['mandateSize']['width']) || ($currentHeight !== $info['mandateSize']['height']))
                                    {
                                        $formField['error'] = true;
                                        $validData = false;
                                        $formField['errorList'][] = 'Please provide an image '.$info['mandateSize']['width'].'px in width and '.$info['mandateSize']['height'].'px in height.';
                                        continue;
                                    }
                                }

                                // Do We Required A Specific Ratio?
                                if(isset($info['mandateRatio']))
                                {
                                    $ratio = imagesx($image) / imagesy($image);
                                    if(floatval($ratio) !== $info['mandateRatio'])
                                    {
                                        $formField['error'] = true;
                                        $validData = false;
                                        $formField['errorList'][] = 'Please provide an image with a dimension ratio of '.$info['mandateRatio'].'.';
                                        continue;
                                    }
                                }
                                // Resizing The Image
                                if(isset($info['maxsize'])){
                                    if(!isset($info['resize'])){
                                        $info['resize'] = 'aspect';
                                    }
                                    $currentWidth = imagesx($image);
                                    $currentHeight = imagesy($image);
                                    $maxWidth = $info['maxsize']['width'];
                                    $maxHeight = $info['maxsize']['height'];
                                    if($info['resize'] === FALSE)
                                    {
                                        if($currentWidth > $maxWidth || $currentHeight > $maxHeight)
                                        {
                                            $formField['error'] = true;
                                            $validData = false;
                                            $formField['errorList'][] = 'Please provide an image no larger than '.$currentWidth.'px in width and '.$currentHeight.'px in height';
                                            continue;
                                        }
                                    }
                                    $resizeRatioX = $maxWidth / $currentWidth;
                                    $resizeRatioY = $maxHeight / $currentHeight;
                                    switch($info['resize']){
                                        case 'aspect':
                                            $resizeRatio = min($resizeRatioX, $resizeRatioY);
                                            if($resizeRatio < 1){
                                                $newWidth = $currentWidth * $resizeRatio;
                                                $newHeight = $currentHeight * $resizeRatio;
                                                $newImage = imagecreatetruecolor($newWidth, $newHeight);
												if($type==='png'||$type==='x-png'){
													imagealphablending($newImage,false); // alpha fix
													imagesavealpha($newImage,true);      // alpha fix
												}
                                                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);
                                            }
                                            break;
                                        case 'crop':
                                            $resizeRatio = max($resizeRatioX,$resizeRatioY);
                                            if($resizeRatio < 1){
                                                $newWidth = $currentWidth * $resizeRatio;
                                                $newHeight = $currentHeight * $resizeRatio;
                                                switch($resizeRatio){
                                                    case $resizeRatioX:
                                                        $startX = 0;
                                                        $startY = ($newHeight - $maxHeight) / 2;
                                                        break;
                                                    case $resizeRatioY:
                                                        $startY = 0;
                                                        $startX = ($newWidth - $maxWidth) / 2;
                                                        break;
                                                }
                                                $newImage = imagecreatetruecolor($maxWidth, $maxHeight);
												if($type==='png'||$type==='x-png'){
													imagealphablending($newImage,false); // alpha fix
													imagesavealpha($newImage,true);      // alpha fix
												}
                                                imagecopyresampled($newImage, $image, 0, 0, $startX, $startY, $maxWidth, $maxHeight, $currentWidth, $currentHeight);
                                            }
                                            break;
                                    }
                                }
                                // Borders On The Image?
                                if(isset($info['border']))
                                {
                                    $color = $info['border']['color'];
                                    $borderColor = imagecolorallocate($newImage,$color['r'],$color['g'],$color['b']);
                                    imagerectangle($newImage,0,0,imagesx($newImage)-1,imagesy($newImage)-1,$borderColor);
                                }
                                // Grayscale?
                                if(isset($info['grayscale']))
                                {
                                    imagefilter($newImage,IMG_FILTER_GRAYSCALE);
                                }
                                // Rounded Corners?
                                if(isset($info['roundedCorners']) && is_array($info['roundedCorners']))
                                {
                                    $radius = $info['roundedCorners']['radius'];
                                    if(!file_exists('themes/'.$info['themeDir'].'/images/corners/rounded_'.$radius.'.png'))
                                    {
                                        $formField['error'] = true;
                                        $validData = false;
                                        $formField['errorList'][] = 'A rounded corner image with radius '.$radius.' could not be found. Please select a different radius.';
                                        continue;
                                    }
                                    $cornerSource = imagecreatefrompng('themes/'.$info['themeDir'].'/images/corners/rounded_'.$radius.'.png');
                                    $cornerWidth = imagesx($cornerSource);
                                    $cornerHeight = imagesy($cornerSource);
                                    $cornerResized = imagecreatetruecolor($radius,$radius);
                                    imagecopyresampled($cornerResized,$cornerSource,0,0,0,0,$radius,$radius,$cornerWidth,$cornerHeight);
                                    $cornerWidth = imagesx($cornerResized);
                                    $cornerHeight = imagesy($cornerResized);
                                    // Allocate colors
                                    $white = imagecolorallocate($newImage,255,255,255);
                                    $black = imagecolorallocate($newImage,0,0,0);
                                    // Size Of Our Image
                                    $size[0] = imagesx($newImage);
                                    $size[1] = imagesy($newImage);
                                    // Top-Left Corner
                                    $dest_x = 0;
                                    $dest_y = 0;
                                    imagecolortransparent($cornerResized,$black);
                                    imagecopymerge($newImage,$cornerResized,$dest_x,$dest_y,0,0,$cornerWidth,$cornerHeight,100);
                                    // Bottom-Left Corner
                                    $dest_x = 0;
                                    $dest_y = $size[1] - $cornerHeight;
                                    $rotated = imagerotate($cornerResized,90,0);
                                    imagecolortransparent($rotated,$black);
                                    imagecopymerge($newImage,$rotated,$dest_x,$dest_y,0,0,$cornerWidth,$cornerHeight,100);
                                    // Bottom-Right Corner
                                    $dest_x = $size[0] - $cornerWidth;
                                    $dest_y = $size[1] - $cornerHeight;
                                    $rotated = imagerotate($cornerResized,180,0);
                                    imagecolortransparent($rotated,$black);
                                    imagecopymerge($newImage,$rotated,$dest_x,$dest_y,0,0,$cornerWidth,$cornerHeight,100);
                                    // Top-Right Corner
                                    $dest_x = $size[0] - $cornerWidth;
                                    $dest_y = 0;
                                    $rotated = imagerotate($cornerResized,270,0);
                                    imagecolortransparent($rotated,$black);
                                    imagecopymerge($newImage,$rotated,$dest_x,$dest_y,0,0,$cornerWidth,$cornerHeight,100);
                                    imagerotate($newImage,0,$white);
                                }
                                // Save The Image
                                if(isset($info['path'])){
                                    $file = $value['name'];
                                    $dir = $info['path'];
                                    if(substr($dir, -1) != '/'){
                                        $dir .= '/';
                                    }
                                    $extension = substr($file, strrpos($file, '.') + 1);
                                    $basename = substr($file, 0, strrpos($file, '.'));
                                    $savePath = $dir;
                                    $savePath .= (isset($info['customName']) && $info['customName'] !== NULL) ? $info['customName'].'.'.$extension : $file;
                                    // Are We Using The CDN?
                                    if(isset($data->cdn) && $data->cdn) {
                                        $quality = (isset($info['quality'])) ? $info['quality'] : 85;
                                        switch($extension){
                                            case 'jpg': case 'jpeg': default:
                                                imagejpeg($newImage, sys_get_temp_dir().'/'.$basename.'.jpg',100);
                                                break;
                                            case 'gif':
                                                imagegif($newImage, sys_get_temp_dir().'/'.$basename.'.gif');
                                                break;
                                            case 'png':
												imagealphablending($newImage,false); // alpha fix
												imagesavealpha($newImage,true);      // alpha fix
                                                imagepng($newImage, sys_get_temp_dir().'/'.$basename.'.png',9);
                                        }
                                        chmod(sys_get_temp_dir().'/'.$basename.'.'.$extension,0777);

                                        $data->cdn->newFile(sys_get_temp_dir().'/'.$basename.'.'.$extension,$savePath,$data->settings['cdnSmall'].$savePath,8);

                                        continue;
                                    }
                                    // Check If Directory Exists; If Not Create It
                                    if(!is_dir($dir))
                                    {
                                        mkdir($dir) or die("The path you specified is not writable : ".$dir);
                                        chmod($dir,0777);
                                    }
                                    @chmod($dir,0777);
                                    if(file_exists($savePath)){
                                        if(!isset($info['overwrite']))
                                        {
                                            $i = 0;
                                            do{
                                                $i++;
                                                $file = (isset($info['customName']) && $info['customName'] !== NULL) ? $info['customName'] : $basename;
                                                $file .= '_' . $i . '.' . $extension;
                                                $savePath = $dir . $file;
                                            }while(file_exists($savePath));
                                        } else {
                                            chmod($savePath,0777);
                                            unlink($savePath);
                                        }
                                    }
                                    $info['savePath'] = $savePath;
                                    $info['saveName'] = $file;
                                    $quality = (isset($info['quality'])) ? $info['quality'] : 85;
                                    switch($extension){
                                        case 'jpg': case 'jpeg': default:
                                            imagejpeg($newImage, $savePath,$quality);
                                            break;
                                        case 'gif':
                                            imagegif($newImage, $savePath);
                                            break;
                                        case 'png':
											imagealphablending($newImage,false); // alpha fix
											imagesavealpha($newImage,true);      // alpha fix
                                            imagepng($newImage, $savePath,9);
                                    }
                                    chmod($savePath,0777);
                                }
                            }
                        }
                    } elseif(isset($formField['required']) && $formField['required']){
                        $validData = false;
                        $formField['error'] = true;
                    }
                } else {
                    $formKey=str_replace('[]','',$formKey);
                    if(array_key_exists($this->formPrefix.$formKey, $_POST)){
                        $value = $_POST[$this->formPrefix.$formKey];

                    }else{
                        $value = '';
                    }
                    if (get_magic_quotes_gpc()) {
                        $value=stripSlashes($value);
                    }

                    $valueField='value';
                    if (!empty($formField['params']['type'])) {
                        switch ($formField['params']['type']) {
                            case 'checkbox':
                                $valueField='checked';
                                $value=(($value=='on') ? 'checked' : '');
                            break;
                        }
                    }
					if (isset($formField[$valueField])) {
						if ($formField[$valueField]!=$value) {
							$formField['updated']=$valueField;
							$formField[$valueField]=$value;
						}
					}
                    if (isset($formField['required']) && $formField['required'] && empty($value)) {
                        $formField['error']=true;
                        $validData=false;
                    } else {
                        if (!empty($formField['validate'])) {
                            switch ($formField['validate']) {
                                case 'eMail':
                                    if (!common_isValidEmail($value)) {
                                        $validData=false;
                                        $formField['error']=true;
                                        $formField['errorList'][]=$formField['eMailFailMessage'];
                                    }
                                break;
                                case 'numeric':
                                    if (is_numeric($value)) {
                                        if (
                                            ( $formField['validateMax'] &&	($value>$formField['validateMax']) ) ||
                                            (	$formField['validateMin'] &&	($value>$formField['validateMin']) )
                                        ) {
                                            $validData=false;
                                            $formField['error']=true;
                                        }
                                    } else {
                                        $validData=false;
                                        $formField['error']=true;
                                    }
                                break;
                            }
                        }
                    }
                    // Check Currency Format
                    if(isset($formField['currency'])) {
                        if($formField['currency']=='USD') {
                            $error=false;
                            // --- Parse Currency ---
                            // Remove spaces
                            $number=str_replace(' ','',$value);
                            // Remove dollar sign
                            $number=str_replace('$','',$number);
                            // Remove commas
                            $number=str_replace(',','',$number);
                            $pos=strpos($number,'.');
                            if($pos===false) {
                                if(is_numeric($number)) {
                                    $this->sendArray[':price']=$number;
                                } else {
                                    $error=true;
                                }
                            } else {
                                if(is_numeric($number)) {
                                    $number=round($number,2);
                                    $this->sendArray[':price']=$number;
                                } else {
                                    $error=true;
                                }
                            }

                            if($error) {
                                $validData=false;
                                $formField['errorList'][] = 'Please correct currency format.';
                                $formField['error'] = true;
                                $this->field[$formField['error']] = true;
                            }
                        }
                    }
                    // Check What It CANNOT Equal
                    if(isset($formField['cannotEqual'])) {
                        $errorMessage = isset($formField['cannotEqualFailMessage']) ? $formField['cannotEqualFailMessage'] : 'Please provide a different value';
                        if(is_array($formField['cannotEqual']))
                        {
                            if(in_array($value,$formField['cannotEqual']))
                            {
                                $validData = false;
                                $formField['errorList'][] = $errorMessage;
                                $formField['error'] = true;
                            }
                        } else {
                            if($value == $formField['cannotEqual'])
                            {
                                $validData = false;
                                $formField['errorList'][] = $errorMessage;
                                $formField['error'] = true;
                            }
                        }
                    }
                    if (isset($formField['compareTo'])) {
                        if ($value != $_POST[$this->formPrefix.$formField['compareTo']]) {
                            $validData = false;
                            $formField['errorList'][] = $formField['compareFailMessage'];
                            $formField['error'] = true;
                            $this->fields[$formField['compareTo']]['error'] = true;
                        }
                    }
                    if(isset($formField['minLength'])) {
                        // if $value >= minLength throw error
                        if(strlen($value) >= (int) $formField['minLength'] || strlen($value) <= 0) {

                        } else {
                            $validData=false;
                            $formField['errorList'][] = $formField['lengthFailMessage'];
                            $formField['error'] = true;
                            $this->field[$formField['error']] = true;
                        }
                    }
                }
			}
		}
		$this->error =! $validData;
		return $validData;
	}
	function populateFromPostData() {
		foreach ($this->fields as $key => $value) {
			$subKey=':'.$key;
			if (
				(!empty($value['params']['type'])) &&
				($value['params']['type']=='checkbox')
			) {
				$this->sendArray[$subKey]=!empty($_POST[$this->formPrefix.$key]);
				$this->fields[$key]['checked']=($this->sendArray[$subKey] ? 'checked': '');
			} elseif (
                (!empty($value['params']['multiple'])) &&
                ($value['params']['multiple']==1)
            ) {
                $key=str_replace('[]','',$key);
                $subKey=str_replace('[]','',$subKey);
                $this->sendArray[$subKey] = array_key_exists($this->formPrefix.$key, $_POST) ? $_POST[$this->formPrefix.$key] : '';
                $this->fields[$key.'[]']['value']=$this->sendArray[$subKey];
            } else {
				$this->sendArray[$subKey] = array_key_exists($this->formPrefix.$key, $_POST) ? $_POST[$this->formPrefix.$key] : '';
				$this->fields[$key]['value']=$this->sendArray[$subKey];
			}
		}
	}
	
	function build($buffer=FALSE,$commonForm=TRUE){
		if($buffer)	{
			ob_start();
		}
		if(function_exists('theme_buildForm')){
			theme_buildForm($this);
		}else{
			$requiredFields=0;
			foreach($this->fields as $thisKey => $formField){
				if($formField['required']){
					$requiredFields++;
				}
			}
			echo '
				<form
					method="post"
					action="',rtrim($this->action,'/').'/','"
					id="',$this->formPrefix,'form"
					enctype="',$this->enctype,'"';
					if($commonForm) echo '
					class="commonForm"';
					echo '
				>';
			if ($this->error) {
				echo '
					<div class="errorBox">',$this->errorText,'</div>';
			}
			$fieldKeys=array_keys($this->fields);
			$priorGroup=NULL;
			echo '
					<div class="fieldsetWrapper">';
			if(!isset($this->fields[$fieldKeys[0]]['group'])){
				$priorGroup=0;
				echo '<fieldset>',(
				!empty($this->caption) ? '
							<legend><span>'.$this->caption.'</span></legend>' :
					''
				);
			}
			foreach ($this->fields as $thisKey => $formField) {
				if(isset($formField['group'])&&$formField['group']!==$priorGroup){
					if(isset($priorGroup)){
						echo '</fieldset>';
					}
					echo '<fieldset class="fg_',common_generateShortName($formField['group'],TRUE),'">
							<legend><span>',$formField['group'],'</span></legend>';
					$priorGroup=$formField['group'];
				}elseif(empty($formField['group'])&&isset($priorGroup)){
					$priorGroup=0;
				}
				if($formField['params']['type']!='hidden') {
					$class=array();
					if ($formField['tag']=='input') {
						if (!empty($formField['params']['type'])) {
							$class[]='type_'.$formField['params']['type'];
						}
					} else $class[]='type_'.$formField['tag'];
					if (!empty($formField['divClasses'])) {
						$class=array_merge($class,$formField['divClasses']);
					}
					$class=implode(' ',$class);
					if (empty($formField['classes'])) {
						$fieldClass=array();
					} else {
						$fieldClass=$formField['classes'];
					}
					if ($formField['required']) {
						$fieldClass[]='required';
					}
					if ($formField['error']) {
						$fieldClass[]='error';
					}
					if (!empty($formField['description'])) {
						$fieldClass[]='nsDesc';
					}
					$fieldClass=implode(' ',$fieldClass);
					echo '
						<div',(
					$class ? ' class="'.$class.'"' : ''
					),'>';
					if(isset($formField['params']['type'])&&!empty($formField['inline'])&&$formField['params']['type']==='checkbox'){ // checkboxes 
						echo '<input
									id="',$this->formPrefix,$thisKey,'"
									name="',$this->formPrefix,$thisKey,'"
								type="checkbox"
							/>';
					}
					if(isset($formField['label'])){
						echo '
								<label for="',$this->formPrefix.$thisKey,'">',$formField['label'],' ',(
						$formField['error'] ? '<b>X</b>' : (
						($formField['required']&&$requiredFields!==count($this->fields)) ? (
						empty($_POST['fromForm']) ?
							'<i>&raquo;</i>' :
							'<span>&radic;</span>'
						) : ''
						)
						),'</label>';
					}
					if(isset($formField['params']['type'])&&!empty($formField['inline'])&&$formField['params']['type']==='checkbox'){
						echo '</div>';
						continue;
					}else{
						echo '<div>';
					}
					echo '
								<',$formField['tag'],'
									id="',$this->formPrefix,$thisKey,'"',(
					($formField['tag']=='span') ? '' : '
									name="'.$this->formPrefix.$thisKey.'"'
					),(
					$fieldClass ? '
									class="'.$fieldClass.'"' : ''
					),(
					empty($formField['checked']) ?	'' : '
									checked="checked"'
					);
					if (!empty($formField['params'])) {
						foreach ($formField['params'] as $attribute => $value) {
							echo '
								',$attribute,'="',$value,'"';
						}
					}
					switch ($formField['tag']) {
						case 'textarea':
							echo '>',html_entity_decode($formField['value'],ENT_QUOTES,'UTF-8'),'</textarea>';
							if (isset($formField['useEditor'])) {
								echo '
		<script type="text/javascript"><!--
			CKEDITOR.replace(\'',$this->formPrefix,$thisKey,'\', {
				customConfig:CMSBasePath+"ckeditor/paladin/config.js"
			});
		--></script>';
							}
							break;
						case 'select':
							echo '>';
							if(isset($formField['type']) && $formField['type']=='timezone'){
								$abbrs=DateTimeZone::listIdentifiers();
								$abbrsCalc=array();
								foreach($abbrs as $abbr){
									$tzObject=new DateTimeZone($abbr);
									$date=new DateTime(NULL,$tzObject);
									$abbrsCalc[$abbr]=$date->format('G:i A');
								}
								natsort($abbrsCalc);
								foreach($abbrsCalc as $identifier => $time){
									echo '<option value="',$identifier,'">',$time,' - ',$identifier,'</option>';
								}
							}else{
								if(isset($formField['type'])&&isset($this->customFormFields[$formField['type']])){
									$formField['options']=$this->customFormFields[$formField['type']];
								}
								$optgroup = FALSE;
								if(!empty($formField['options'])) {
									foreach ($formField['options'] as $key => $option) {
										$selected='';
										if(empty($formField['value'])) {
											// Selected
											if(isset($formField['selected'])) {
												if(is_array($formField['selected'])) {
													foreach($formField['selected'] as $value) {
														if(is_array($option)) {
															if($value==$option['value']) {
																$selected=' selected="selected"';
															}
														} else {
															if($value==$option) {
																$selected=' selected="selected"';
															}
														}
													}
												} else {
													if(is_array($option)) {
														if($formField['selected']==$option['value']) {
															$selected=' selected="selected"';
														}
													} else {
														if($formField['selected']==$option) {
															$selected=' selected="selected"';
														}
													}
												}
											}
										} else {
											// Return bad entry
											if($formField['value']==$option['value']) {
												$selected=' selected="selected"';
											}
											if(is_array($formField['value'])) {
												foreach($formField['value'] as $value) {
													if(is_array($option)) {
														if($value==$option['value']) {
															$selected=' selected="selected"';
														}
													} else {
														if($value==$option) {
															$selected=' selected="selected"';
														}
													}
												}
											} else {
												if(is_array($option)) {
													if($formField['value']==$option['value']) {
														$selected=' selected="selected"';
													}
												} else {
													if($formField['value']==$option) {
														$selected=' selected="selected"';
													}
												}
											}
										}
										if (is_array($option)) {
											if(isset($option['optgroup']) && $option['optgroup'] !== $optgroup)
											{
												if($optgroup !== FALSE)
												{
													echo '
													</optgroup>';
												}
												$optgroup = $option['optgroup'];
												echo '
											<optgroup label = "',$option['optgroup'],'">';
											}
											echo '
											<option',$selected,' value="',$option['value'],'">',$option['text'],'</option>';
											if(!isset($formField['options'][$key+1]) && $optgroup)
											{
												echo '</optgroup>';
											}
										} else {
											echo '
											<option',$selected,'>',$option,'</option>';
										}
									}
								}
							}
							echo '
								</select>';
							break;
						case 'span':
							echo '>',htmlentities($formField['value'],ENT_QUOTES,'UTF-8'),'</span>';
							break;
						default:
							if (!empty($formField['value'])) {
								echo '
									value="',htmlentities($formField['value'],ENT_QUOTES,'UTF-8'),'"';
							}
							echo '
								/>';
					}
					echo '
							</div>';
					if (count($formField['errorList'])>0) {
						echo '
							<ul class="errorMessages">';
						foreach ($formField['errorList'] as $message) {
							echo '
								<li>',$message,'</li>';
						}
						echo '
							</ul>';
					}
					echo '
						</div>';
				}
			}
			echo '
					</fieldset></div>
					<div class="submitsAndHiddens">
						<input type="submit" class="submit" value="',$this->submitTitle,'" />
						<input type="hidden" name="fromForm" id="fromForm" value="',$this->fromForm,'" />';
			foreach ($this->fields as $thisKey => $formField) {
				if ($formField['params']['type']=='hidden') {
					echo '
					<input type="hidden"
							name="',$this->formPrefix.$thisKey,'"
							id="',$this->formPrefix.$thisKey,'"
						value="',$formField['value'],'"
					/>';
				}
			}
	  if(count($this->fields)&&($this->error||$requiredFields!==count($this->fields))){
				echo '<div class="details">',
				($requiredFields!==count($this->fields)?'<i>&raquo;</i> Indicates a required field':''),
				($this->error ? ' <b>X</b> indicates a field with errors' : ''),'
					</div>
				';
      }
      if(strlen($this->extraMarkup)!==0) {
        echo '
          <div class="extraMarkup">
            ',$this->extraMarkup.'
          <!-- .extraMarkup --></div>';
      }
      echo '
          <!-- .submitsAndHiddens --></div>
        </form>';
		}
		if($buffer)	{
			$contents = ob_get_clean();
			return $contents;
		}
	}
}