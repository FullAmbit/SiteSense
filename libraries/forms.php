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
	function __construct($fields, $dataName, $formName, $data=false, $admin=false, $action=NULL){
		if($data !== false && $action == NULL){
			$this->action = $data->linkRoot . implode('/', array_filter($data->action));
		} else {
			$this->action = $action;
		}
		$this->formPrefix = $dataName . '_';
		$this->fromForm = $dataName;
		$this->fields = $fields;
		$this->submitTitle = 'Submit';
		$this->caption = $formName;
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
		foreach($this->fields as &$field){
			$field = array_merge($defaults, $field);
			if(!isset($field['params']['type'])){
				$field['params']['type'] = '';
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
		$submitTitle,
		$fromForm,
		$fields = array(),
		$sendArray=array(),
		$error=false,
		$errorText='',
		$extraMarkup='',
		$randomName = '',
		$contentAfter,
		$ajax,
		$disableAjax = FALSE,
		$filetypes;
	function __construct($dataName,$data=false,$admin=false) {
		
		//Just some defaults to potentially save time in the formArray files.
		if($data !== false){
			$this->action = $data->linkRoot . implode('/', array_filter($data->action));
			$this->ajax = ($data->currentPage == 'ajax') ? true : false;
		}
		$this->randomName = substr(md5(rand() * time()),0,10);
		$this->formPrefix = $dataName . '_';
		$this->fromForm = $dataName;

        if($admin) {
            $moduleName=array_search($data->action[1],$data->output['moduleShortName']);
            $target='modules/'.$moduleName.'/admin/forms/'.$moduleName.'.admin.form.'.$dataName.'.php';
        } else {
            $moduleName=array_search($data->action[0],$data->output['moduleShortName']);
            $target='modules/'.$moduleName.'/forms/'.$moduleName.'.form.'.$dataName.'.php';
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
            if(isset($fieldData['tag']) && $fieldData['tag']=='span'){
                $hiddenFields[$field.'_hidden']= array(
                    'tag' => 'input',
                    'params' => array(
                        'type' => 'hidden'
                    ),
                    'value' => $fieldData['value']
                );
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
	function validateFromPost(&$data = FALSE) {
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
							$extension = substr($fileName, strrpos($fileName, '.') + 1); 
							$basename = substr($fileName, 0, strrpos($fileName, '.'));
							
							// Mandating Extension
							if(isset($info['mandateExt']) && $info['mandateExt'] !== FALSE && !in_array($extension,$info['mandateExt']))
							{
								$formField['error'] = true;
								$validData = false;
								$formField['errorList'][] = 'Please provide a file with the extension '.implode(',',$info['mandateExt']).'.';
								//continue;
							}
							// Mandating File-Type
							if(isset($info['mandateType']) && $info['mandateType'] !== FALSE && !in_array($fileType,$info['mandateType']))
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
										$formField['errorList'][] = 'Please provide a '.$info['mandateType'].' image.';
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
										//var_dump("RATIO");
										//var_dump($ratio);
										//var_dump($info['mandateRatio']);
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
									//var_dump($info);
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
									if($data->cdn)
									{	
										$quality = (isset($info['quality'])) ? $info['quality'] : 85;
										switch($extension){
											case 'jpg': case 'jpeg': default:
												imagejpeg($newImage, sys_get_temp_dir().$basename.'.jpg',100);
												break;
											case 'gif':
												imagegif($newImage, sys_get_temp_dir().$basename.'.gif');
												break;
											case 'png':
												imagepng($newImage, sys_get_temp_dir().$basename.'.png',9);
										}
										chmod(sys_get_temp_dir().$basename.'.'.$extension,0777);
																												
										$data->cdn->newFile(sys_get_temp_dir().$basename.'.'.$extension,$savePath,$data->settings['cdnSmall'].$savePath,8);
										
										continue;
									}
									
									// Check If Directory Exists; If Not Create It
									if(!is_dir($dir))
									{
										mkdir($dir);
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
											imagepng($newImage, $savePath,9);
											//$optimizedName = ($info['customName'] !== NULL) ? $info['customName'] : $basename;
											//$optimizedPath = $dir . $optimizedName . '_optimized.'.$extension;
											//system("pngcrush -brute -l 9 ".$savePath." ".$optimizedPath."",$status);
											//link($savePath,$optimizedPath);
									}
									chmod($savePath,0777);
								}
							}
						}
					}else if(isset($formField['required']) && $formField['required']){
						$validData = false;
						$formField['error'] = true;
					}
				}else{
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
					if ($formField[$valueField]!=$value) {
						$formField['updated']=$valueField;
						$formField[$valueField]=$value;
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
					// Check What It CANNOT Equal
					if(isset($formField['cannotEqual']))
					{
						
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
					//	if ($value!=htmlspecialchars($_POST[$this->formPrefix.$formField['compareTo']])) { // why the htmlspecialchars?
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
			} else {
				$this->sendArray[$subKey] = array_key_exists($this->formPrefix.$key, $_POST) ? $_POST[$this->formPrefix.$key] : '';
				$this->fields[$key]['value']=$this->sendArray[$subKey];
			}
		}
	}
}
?>