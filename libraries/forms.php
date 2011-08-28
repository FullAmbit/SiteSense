<?php
class customFormHandler extends formHandler{
	function __construct($fields, $dataName, $formName, $data=false, $admin=false){
		if($data !== false){
			$this->action = $data->linkRoot . implode('/', array_filter($data->action));
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
		$formPrefix,
		$caption,
		$submitTitle,
		$formForm,
		$fields = array(),
		$sendArray=array(),
		$error=false,
		$errorText='',
		$extraMarkup='';

	function __construct($dataName,$data=false,$admin=false) {
		//Just some defaults to potentially save time in the formArray files.
		if($data !== false){
			$this->action = $data->linkRoot . implode('/', array_filter($data->action));
		}
		$this->formPrefix = $dataName . '_';
		$this->fromForm = $dataName;
		require_once(($admin ? 'admin/' : '').'forms/'.$dataName.'.formArray.php');
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

	function validateFromPost() {
		$validData=true;
		if (isset($_POST['fromForm'])) {
			foreach ($this->fields as $formKey => &$formField) {
				$formField['errorList']=array();
				if($formField['params']['type'] == 'file'){
					if(array_key_exists($this->formPrefix.$formKey, $_FILES)){
						$value = &$_FILES[$this->formPrefix.$formKey];
						if(is_array($formField['images'])){
							switch($value['type']){
								case 'image/jpeg': case 'image/pjpeg':
									$type = 'jpeg';
									break;
								case 'image/png' : case 'image/x-png':
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
							$function = 'imagecreatefrom' . $type;
							$image = $function($value['tmp_name']);
							foreach($formField['images'] as &$info){
								$newImage = $image;
								if(isset($info['maxsize'])){
									$currentWidth = imagesx($image);
									$currentHeight = imagesy($image);
									$maxWidth = $info['maxsize']['width'];
									$maxHeight = $info['maxsize']['height'];
									$resizeRatioX = $maxWidth / $currentWidth;
									$resizeRatioY = $maxHeight / $currentHeight;
									$resizeRatio = min($resizeRatioX, $resizeRatioY);
									if($resizeRatio < 1){
										$newWidth = $currentWidth * $resizeRatio;
										$newHeight = $currentHeight * $resizeRatio;
										$newImage = imagecreatetruecolor($newWidth, $newHeight);
										imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);
									}
								}
								if(isset($info['path'])){
									$file = $value['name'];
									$dir = $info['path'];
									if(substr($dir, -1) != '/'){
										$dir .= '/';
									}
									$extension = substr($file, strrpos($file, '.') + 1); 
									$basename = substr($file, 0, strrpos($file, '.'));
									$savePath = $dir . $file;
									if(file_exists($savePath)){
										$i = 0;
										do{
											$i++;
											$file = $basename . '_' . $i . '.' . $extension;
											$savePath = $dir . $file;
										}while(file_exists($savePath));
									}
									$info['savePath'] = $savePath;
									$info['saveName'] = $file;
									switch($extension){
										case 'jpg': case 'jpeg': default:
											imagejpeg($newImage, $savePath);
											break;
										case 'gif':
											imagegif($newImage, $savePath);
											break;
										case 'png':
											imagepng($newImage, $savePath);
									}
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
					if (isset($formField['compareTo'])) {
					//	if ($value!=htmlspecialchars($_POST[$this->formPrefix.$formField['compareTo']])) { // why the htmlspecialchars?
						if ($value != $_POST[$this->formPrefix.$formField['compareTo']]) {
							$validData = false;
							$formField['errorList'][] = $formField['compareFailMessage'];
							$formField['error'] = true;
							$this->fields[$formField['compareTo']]['error'] = true;
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
				$this->fields[$key]['checked']=(
					$this->sendArray[$subKey] ? 'checked' : ''
				);
			} else {
				$this->sendArray[$subKey] = array_key_exists($this->formPrefix.$key, $_POST) ? $_POST[$this->formPrefix.$key] : '';
				$this->fields[$key]['value']=$this->sendArray[$subKey];
			}
		}
	}

}

?>