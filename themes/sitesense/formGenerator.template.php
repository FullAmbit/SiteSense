<?php
function theme_buildForm($formData) {
	echo '
		<form
			method="post"
			action="',$formData->action,'"
			id="',$formData->formPrefix,'form"
			enctype="multipart/form-data"
			class="commonForm"
		>';
	if ($formData->error) {
		echo '
			<div class="errorBox">',$formData->errorText,'</div>';
	}
	echo '
			<div class="fieldsetWrapper"><fieldset>',(
				isset($formData->caption) ? '
				<legend><span>'.$formData->caption.'</span></legend>' :
				''
			);
	foreach ($formData->fields as $formDataKey => $formField) {
		if ($formField['params']['type']!='hidden') {
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
				),'>
					<label for="',$formData->formPrefix.$formDataKey,'">',$formField['label'],' ',(
						$formField['error'] ? '<b>X</b>' : (
							$formField['required'] ? (
								empty($_POST['fromForm']) ?
								'<i>&raquo;</i>' :
								'<span>&radic;</span>'
							) : ''
						)
					),'</label>
					<div>
						<',$formField['tag'],'
							id="',$formData->formPrefix,$formDataKey,'"',(
								($formField['tag']=='span') ? '' : '
							name="'.$formData->formPrefix.$formDataKey.'"'
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
					echo '>',htmlspecialchars_decode($formField['value']),'</textarea>';
					if (isset($formField['useEditor'])) {
						echo '
<script type="text/javascript"><!--
	CKEDITOR.replace(\'',$formData->formPrefix,$formDataKey,'\', {
		customConfig:CMSBasePath+"ckeditor/paladin/config.js"
	});
--></script>';
					}
				break;
				case 'select':
					echo '
						>';
					
					foreach ($formField['options'] as $key => $option) {
						if (is_array($option)) {
							echo '
							<option',(
								($formField['value']==$option['value']) ?
								' selected="selected"' :
								''
							),' value="',$option['value'],'">',$option['text'],'</option>';
						} else {
							echo '
							<option',(
								($formField['value']==$option) ?
								' selected="selected"' :
								''
							),'>',$option,'</option>';
						}
					}
					echo '
						</select>';
				break;
				case 'span':
					echo '>',htmlspecialchars($formField['value']),'</span>';
				break;
				default:
					if (!empty($formField['value'])) {
						echo '
							value="',htmlspecialchars($formField['value']),'"';
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
				<input type="submit" class="submit" value="',$formData->submitTitle,'" />
				<input type="hidden" name="fromForm" id="fromForm" value="',$formData->fromForm,'" />';
	foreach ($formData->fields as $formDataKey => $formField) {
		if ($formField['params']['type']=='hidden') {
			echo '
		    <input type="hidden"
					name="',$formData->formPrefix.$formDataKey,'"
					id="',$formData->formPrefix.$formDataKey,'"
		    	value="',$formField['value'],'"
		    />';
		}
	}
	echo '
				<i>&raquo;</i> Indicates a required field',(
					$formData->error ? ', <b>X</b> indicates a field with errors' : ''
				),(
					(strlen($formData->extraMarkup)==0) ? '' : $formData->extraMarkup
				),'
			<!-- .submitsAndHiddens --></div>
		</form>';
}
?>