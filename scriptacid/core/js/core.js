//TODO: посмотреть реализацию parse_url() в php-js, применить для парсинга SACID.urlHash


function RedirectTo(url, timeOut) {
	if (url.length > 0) {
		if (timeOut > 0) {
			setTimeout(function() {location = url;}, timeOut);
		} else {
			location = url;
		}
		return false;
	} else {
		return false;	
	}
}
function de(variable) {
	if(console != undefined) {
		console.log(variable);
	}
}

/**
 * ScriptAcid core js-utils
 * @author pr0n1x
 */
var SACID = {
	/**
	 * Объект отслеживающий измение хеша вида #!urlParam1=value1&urlParam2=value2
	 */
	urlHash : {
	
			parse: function() {
				regHashGet = /^(\#\!){1}(.*)/i;
				var obHashGet = new Object();
				var countParams = 0;
				if( (arMatches = location.hash.match(regHashGet)) ) {
					hashGet = arMatches[2];
					this.curUrlHashRequest = hashGet;
					var arHashGet = hashGet.split("&");
					for(key in arHashGet) {
						arHashGetValTmp = arHashGet[key].split("=");
						var hashGetVarName = arHashGetValTmp[0];
						var hashGetVarValue = arHashGetValTmp[1];
						if( hashGetVarName.match(/^[a-z0-9\_\-]*$/i) ) {
							obHashGet[hashGetVarName] = hashGetVarValue;
							countParams++;
						}
					}
				}
				return {
					count: countParams,
					params: obHashGet
				};
			},
	
			getChangedParams: function(obParse) {
				var debug = false;
				var obChanged = new Object();
				var obChanged_length = 0;
				var obNewParams = obParse.params;
				var countNewParams = obParse.count;
				if(debug) {
					console.log("Old:");
					console.log(this.obParams);
					console.log("New:");
					console.log(obNewParams);	
				}
	
				// Ищем новые или измененные параметры
				for(key in obNewParams) {
					if(debug) {
						console.log("obNewParams["+key+"] = " + obNewParams[key]);
						console.log("this.obParams["+key+"] = " + this.obParams[key]);
					}
					if( !this.obParams.hasOwnProperty(key) ) {
						if(debug) console.log("Found new: " + key);
						obChanged[key] = null;
						obChanged_length++;
					}
					else if(this.obParams[key] != obNewParams[key]) {
						if(debug) console.log("Found changed: " + key);
						obChanged[key] = this.obParams[key];
						obChanged_length++;
					}
				}
				// Ищем удаленные параметры
				for(key in this.obParams) {
					if( !obNewParams.hasOwnProperty(key) ) {
						if(debug) console.log("Found deleted: " + key);
						obChanged[key] = this.obParams[key];
						obChanged_length++;
					}
				}
				
				var obReturn = {
					count: obChanged_length,
					params: obChanged
				};
				if(debug) console.log(obReturn);
	
				return obReturn;
			},
			
			onHashChange: function() {
				var newParse = this.parse();
				var ChangedParams = this.getChangedParams(newParse);
				// fix. if hash not changed. just loaded.
				if(ChangedParams.count<1) {
					ChangedParams = newParse;
				}
				this.obParams = newParse.params;
				this.countParams = newParse.count;
				this.obParamsChanged = ChangedParams.params;
				this.countChangedParams = ChangedParams.count;
			},
	
			// constructor
			init: function() {
				this.countParams = 0;
				this.countChangedParams = 0;
				
				var initParse = this.parse();
				this.obParams = initParse.params;
				this.countParams = initParse.count;
				
				this.obParamsChanged = initParse.params;
				this.countChangedParams = initParse.count;
			}
	},

	Components : {
			init: function() {
				
			},
			
			ob_POST: {},
			obAjaxComponentList: {},
			
			addAjaxComponent: function(componentName, componentCallKey, obRequestDeps) {
				//console.log(componentName);
				var isRelatedFromRequestGet = false;
				for(key in obRequestDeps["GET"]) {
					isRelatedFromRequestGet = true;
					break;
				}
				var isRelatedFromRequestPost = false;
				for(key in obRequestDeps["POST"]) {
					isRelatedFromRequestPost = true;
					break;
				}
				this.obAjaxComponentList[componentCallKey] = {
					"componentName": componentName,
					"callKey": componentCallKey,
					"countCalls": 0,
					"allowCall": true,
					"myRequestHashParamChanged": false,
					"isRelatedFromRequestGet": isRelatedFromRequestGet,
					"isRelatedFromRequestPost": isRelatedFromRequestPost,
					"obRequestDeps": obRequestDeps
				};
				//console.log(this.obAjaxComponentList[componentCallKey]);
			},
			
			onHashChange: function() {
				for(componentCallKey in this.obAjaxComponentList) {
					var curCmp = this.obAjaxComponentList[componentCallKey];
					
					var obPost = Object();
					var arRequestParams = Array();
					var strRequestParams = "";
					if(curCmp.isRelatedFromRequestGet) {
						arRequestParams = this.__getRequestGetParams(curCmp);
						strRequestParams = this.__getRequestGetStringFromArray(arRequestParams);
					}
					if(curCmp.isRelatedFromRequestPost) {
						for(key in curCmp.obRequestDeps['POST']) {
							if(curCmp.obRequestDeps['POST'][key] && this.ob_POST[key]) {
								obPost[key] = this.ob_POST[key]; 
							}
						}
					}
					
					//console.log(curCmp);
					//console.log(curCmp.countCalls);
					//console.log(curCmp.myRequestHashParamChanged);
					if(
						curCmp.countCalls == 0
						||
						(curCmp.myRequestHashParamChanged /*&& curCmp.isRelatedFromRequestGet*/ )
					) {
						this.callAjax(componentCallKey, strRequestParams, obPost);
					}
				}
				//console.log('-----------------------');
			},
			
			__getRequestGetParams: function(curCmp) {
				var arRequestParams = new Array();
				curCmp.myRequestHashParamChanged = false;
				for(requestGetParamName in curCmp.obRequestDeps["GET"]) {
					if(
						SACID.urlHash.obParamsChanged.hasOwnProperty(requestGetParamName)
						&&
						SACID.urlHash.obParams[requestGetParamName]
					) {
						curCmp.myRequestHashParamChanged = true;
					}
					if(SACID.urlHash.obParams.hasOwnProperty(requestGetParamName)) {
						arRequestParams[arRequestParams.length] = {
							name: requestGetParamName,
							value: SACID.urlHash.obParams[requestGetParamName]
						};
					}
					else {
						arRequestParams[arRequestParams.length] = {
							name: requestGetParamName,
							value: this.getUriParam(requestGetParamName)
						};
					}
					
				}
				//console.log('1111');
				//console.log(arRequestParams);
				return arRequestParams;
			},
			__getRequestGetStringFromArray: function(arRequestParams) {
				var strRequestParams = '';
				for(key in arRequestParams) {
					strRequestParams += '&' + arRequestParams[key].name + '=' + arRequestParams[key].value; 
				}
				return strRequestParams;
			},
				
			callAjax: function(componentCallKey, strParams, obPost) {
				// Объект текущего аджакс-компонента
				var curCmp = this.obAjaxComponentList[componentCallKey];
				// JQ-Объект. Див компонента в который будет положен результат аджакс-вызова
				var $cmpDiv = jQuery('#sacid-cmp-ajax-call-key-' + componentCallKey);
				curCmp['$cmpDiv'] = $cmpDiv;
				// Url-запроса
				strGet = '/scriptacid/core/ajax/call_ajax_component.php?component_call_key=' + componentCallKey + strParams;
				
				this.__setContentOnLoad(curCmp);
				
				var $ajaxRequest = {
					type: 'POST',
					data: obPost,
					url: strGet,
					success: this.__fnAjaxSuccess,
					componentCallKey: componentCallKey,
					strParams: strParams,
					curCmp: curCmp
				};
				jQuery.ajax($ajaxRequest);
			},
			// Ф-ия-коллбэк на аджакс вызов. Если аджакс вызов прошел удачно, то вызовется эта ф-ия
			__fnAjaxSuccess: function(data) {
				SACID.Components.__setContentAfterLoad(this.curCmp, data);
				var componentCallKey = this.componentCallKey;
				var curCmp = SACID.Components.obAjaxComponentList[componentCallKey];
				curCmp.countCalls++;
				var strRequestGetForForm = this.strParams;
					// Сразу после загрузки компонента, цепляем событие на отправку форм, если таковые есть
					this.curCmp.$cmpDiv.find('form').submit(function() {
						var arFormFields = jQuery(this).serializeArray();
						var arFormData = SACID.__getSerializedFormAsObject(arFormFields)
						SACID.Components.callAjax(componentCallKey, strRequestGetForForm, arFormData);
						return false;
					});
			},
			// Здесь просто делаем красивую загрузку
			__setContentOnLoad: function(curCmp) {
				// Очищаем. внутри этого дива javascript с вызовом addComponent(), что бы не вывполнился очищаем.
				if(curCmp.countCalls == 0) {
					curCmp.$cmpDiv.html('');
				}
				curCmp.$cmpDiv.attr('style', 'position: relative; min-height: 56px; min-width: 56px;');
				var cmpDivWidth = curCmp.$cmpDiv.width();
				var cmpDivHeight = curCmp.$cmpDiv.height();
				strCmpDivMask = ''
					+'<div class="component-ajaxload-mask" style="'
							+'position: absolute;'
							+'z-index: 10000;'
							+'background: url(/scriptacid/core/img/ajax-load-bg.png);'
							+'-moz-border-radius: 4px;'
							+'-webkit-border-radius: 4px;'
							+'-o-border-radius: 4px;'
							+'-khtml-border-radius: 4px;'
							+'border-radius: 4px;'
							+'min-width: 56px;'
							+'min-height: 56px;'
							+'width: ' + (cmpDivWidth + 8) + 'px;'
							+'height: ' + (cmpDivHeight + 9) + 'px;'
							+'top: -4px;'
							+'left: -4px;'
					+'">'
						+'<img style="margin: 20px;" src="/scriptacid/core/img/ajax-load-1980AF.gif" />'
					+'</div>'
				;
				curCmp.$cmpDiv.css('position: relative;');
				curCmp.$cmpDiv.html( strCmpDivMask + curCmp.$cmpDiv.html() );
			},
			// Эта ф-ия убирает все ошметки от созданных нами доп элементов в ф-ии setContentOnLoad
			__setContentAfterLoad: function(curCmp, data) {
				//data.replace('<sacid:javascript>', '<script type="text/javascript">');
				//data.replace('</sacid:javascript>', '</script>');
				//console.log(data);
				curCmp.$cmpDiv.attr('style', '');
				curCmp.$cmpDiv.html(data);
			},
			
			getUriParam: function(name) {
				name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
				var regexS = "[\\?&]"+name+"=([^&#]*)";
				var regex = new RegExp( regexS );
				var results = regex.exec( window.location.href );
				if( results == null )
					return "";
				else
					return results[1];
			},

			// Показать форму редактирования
			showEditForm: function(obComponentCall) {
				de(obComponentCall);
				// Проверяем корректность присланного объекта вызова компонента
				if(!obComponentCall.componentName) {
					return false;
				}
				if( !obComponentCall.templateName ) {
					return false;
				}
				if( !obComponentCall.componentCallKey ) {
					return false;
				}
				if( !obComponentCall.obArParams ) {
					return false;
				}
				if( !obComponentCall.inFile ) {
					return false;
				}
				if( !obComponentCall.inFileComponentNum<0 ) {
					return false;
				}
				
				
				var $componentEditForm = jQuery('#component-edit-form-'+obComponentCall.componentCallKey);
				// Если элемент div с формой редактирования не создан, создаем
				if($componentEditForm.length == 0) {
					// Создаем Пустой див для формы редактирования компонента
					jQuery('body').append(''
						+'<div class="sacid-cmp-edit-form"'
							+' id="component-edit-form-'+obComponentCall.componentCallKey+'"'
							+' style="position: relative;"'
						+'>'
							+'<div class="sacid-loading-mask" style="'
								+'position: absolute;'
								+'z-index: 10000;'
								+'background: url(/scriptacid/core/img/ajax-load-bg.png);'
								+'-moz-border-radius: 4px;'
								+'-webkit-border-radius: 4px;'
								+'-o-border-radius: 4px;'
								+'-khtml-border-radius: 4px;'
								+'border-radius: 4px;'
								+'width: 100%;'
								+'height: 95%;'
							+'">'
								+'<img style="margin: 20px;" src="/scriptacid/core/img/ajax-load-1980AF.gif" />'
							+'</div>'
							+'<div class="sacid-cmp-edit-form-messages"></div>'
							+'<div class="sacid-cmp-edit-form-content" style="'
								+'width: 100%;'
								+'height: 95%;'
							+'">'
							+'</div>'
						+'</div>'
					);
					// тут же получаем
					$componentEditForm = jQuery('#component-edit-form-'+obComponentCall.componentCallKey);

					//de(obComponentCall);
					obComponentCall.saveButtonLock = true;
					// Создаем в дом-элементе дива объект содеращий информацию о компоненте
					$componentEditForm.get(0).SACIDComponentCall = obComponentCall;
					// Подключем к созданному div'у диалог (оборачиваем вокруг div'а диалог)
					$componentEditForm.dialog({
						bgiframe: true,
						autoOpen: false,
						width: 640,
						height: 480,
						//height: 180,
						modal: true,
						title: 'Редакт',
						buttons: {
								'Сохранить': function() {
									//jQuery(this).html('<p>Визуальное редактирование параметров пока не реализовано!</p>');
									
									// Если кнопка сохранить заблокирована, то просто закрываем.
									if(this.SACIDComponentCall.saveButtonLock) {
										//jQuery(this).dialog('close');
										return false;
									}
									var arFormFields = jQuery(this).find('form').serializeArray();
									var arFormData = SACID.__getSerializedFormAsObject(arFormFields)
									var obPost = {
										currentCall: {
											componentName: this.SACIDComponentCall.componentName,
											templateName: this.SACIDComponentCall.templateName,
											templateSkin: this.SACIDComponentCall.templateSkin,
											templateSkinsList: this.SACIDComponentCall.templateSkinsList,
											inFile: this.SACIDComponentCall.inFile,
											inFileComponentNum: this.SACIDComponentCall.inFileComponentNum,
											obArParams: this.SACIDComponentCall.obArParams
										},
										replacementCall: {
											componentName: this.SACIDComponentCall.componentName,
											templateName: arFormData['template']['name'],
											templateSkin: arFormData['template']['skin'],
											inFile: this.SACIDComponentCall.inFile,
											inFileComponentNum: this.SACIDComponentCall.inFileComponentNum,
											obArParams: arFormData['parameters']
										}
									};
									//de(obPost);
									true && jQuery.ajax({
										type: 'POST',
										url: '/scriptacid/core/ajax/replace_component.php',
										data: obPost, 
										success: function(data) {
											//de(data);
											var bSuccess = false;
											if(data == 'ok.') {
												bSuccess = true;
											}
											var $forElement = jQuery(this.forElement);
											if(bSuccess) {
												// пишем полученные данные
												$forElement.find('.sacid-cmp-edit-form-messages').html('Компонент сохранен.');
												// Убираем показатель загрузки
												$forElement.find('.sacid-loading-mask').css({
													'display': 'none'
												});
												window.location.reload();
											}
											else {
												$forElement.find('.sacid-cmp-edit-form-messages').html('Ошибка: ' + data);
												$forElement.find('.sacid-loading-mask').css({
													'display': 'none'
												});
											}
										},
										// Передаем в объект аджакс-запроса элемент в который надо поместить ответ
										forElement: this
									});
								},
								'Отмена': function() {
									jQuery(this).dialog('close');
								}
						},
						// Открываем диалог, получаем параметры
						open: function() {
							//de(this);
							SACID.Components.__getEditFormByAjax(this);
						},
						close: function() {
							
						}
					});
				}
				$componentEditForm.dialog('open');
				return true;
			},
			__getEditFormByAjax: function(divEditForm, templateName) {
				if(templateName == undefined) {
					templateName = divEditForm.SACIDComponentCall.templateName+':'+divEditForm.SACIDComponentCall.templateSkin
				}
				//de(divEditForm);
				//de(templateName);

				// Блокируем кнопку сохранить
				divEditForm.SACIDComponentCall.saveButtonLock = true;
				// Показываем что загужаемся
				jQuery(divEditForm).find('.sacid-loading-mask').css({
					'display': 'block'
				});
				// Очищаем див для формы
				jQuery(divEditForm).find('.sacid-cmp-edit-form-messages').html('');
				jQuery(divEditForm).find('.sacid-cmp-edit-form-content').html('');
				// Делаем аджакс запрос на получения формы
				jQuery.ajax({
					type: 'POST',
					url: '/scriptacid/core/ajax/get_cmp_params_edit_form.php'
							+'?component_name='+divEditForm.SACIDComponentCall.componentName
							+'&template_name='+templateName
					,
					data: {current_params: divEditForm.SACIDComponentCall.obArParams},
					success: function(data) {
						// Разблокируем кнопку сохранить
						var forThisElement = this.forElement;
						//de(forThisElement);
						forThisElement.SACIDComponentCall.saveButtonLock = false;
						// дом-элемент для ответа
						var $forElement = jQuery(forThisElement);
						// пишем полученные данные
						$forElement.find('.sacid-cmp-edit-form-content').html(data);
						// Убираем показатель загрузки
						$forElement.find('.sacid-loading-mask').css({
							'display': 'none'
						});


						var $templateNameCurrentActiveOption = $forElement.find('select.template_name option[selected]');
						var $templateSkinCurrentActiveOption = $forElement.find('select.template_skin option[selected]');
						$forElement.find('select.template_name').change(function() {
							//de($templateNameSelectedOption);
							var $options = jQuery(this).find('option');
							var $optionSelected = jQuery(this).find('option[selected]');
							//de(forThisElement);
							if (confirm('Меняйте шаблон компонента в первую очередь.'
										+' Все изменения формы будут потеряны.'
										+' Вы уверены, что хотите сменить шаблон компонента сейчас?')
							) {
								SACID.Components.__getEditFormByAjax(
									forThisElement,
									$optionSelected.val()+':'+$templateSkinCurrentActiveOption.val()
								);
							}
							else {
								//de($optionSelected);
								//de($options);
								$options.removeAttr('selected');
								$templateNameCurrentActiveOption.attr('selected', 'selected');
								//alert("Tогда оставайтесь");
							}

							
						});
					},
					// Передаем в объект аджакс-запроса элемент в который надо поместить ответ
					forElement: divEditForm
				});
			}
	},
	
	
	// TOOLS
	__getSerializedFormAsObject: function(arFormFields) {
			var getParentNodeByNameChain = function(obFieldsTree, arNameChain, depth) {
				var parentNode = obFieldsTree;
				for(var index=1; index<=depth; index++) {
					//var dumpCurParent = parentNode; 
					parentNode = parentNode[arNameChain[index-1]];
				}
				return parentNode;
			}
		
			var obFieldsTree = {};
			for(key in arFormFields) {
				var formParamName = arFormFields[key].name;
				var formParamValue = arFormFields[key].value;

				var arFormParamNameChain = formParamName.split('[');
				var arAutoIndex = {};
				for(var depth=0; depth<arFormParamNameChain.length; depth++) {
					// removing last "]" from names 
					if( arFormParamNameChain[depth][arFormParamNameChain[depth].length-1] == ']' ) {
						arFormParamNameChain[depth] = arFormParamNameChain[depth].substr(0, arFormParamNameChain[depth].length-1);
					}
					var curIndexName = arFormParamNameChain[depth];
					
					// makeing tree from name-chain
					if(arFormParamNameChain.length - depth == 1) {
						var parentNode = null;
						if(curIndexName == '') {
							parentNode = getParentNodeByNameChain(obFieldsTree, arFormParamNameChain, depth);
							parentNode.__itemsCount__++
							parentNode[parentNode.__itemsCount__] = formParamValue;
						}
						else {
							parentNode = getParentNodeByNameChain(obFieldsTree, arFormParamNameChain, depth);
							parentNode[curIndexName] = formParamValue;
						}
						break;
					}
					else {
						parentNode = getParentNodeByNameChain(obFieldsTree, arFormParamNameChain, depth);
						if(	parentNode[curIndexName] == undefined ) {
							parentNode[curIndexName] = {__itemsCount__: 0};
						}
						
					}
				}
			}
			var removeItemsCount = function(obTree) {
				for(key in obTree) {
					if(key == '__itemsCount__') {
						delete obTree[key];
					}
					else if(obTree[key].constructor == Object) {
						removeItemsCount(obTree[key]);
					}
				}
				return obTree;
			}
			removeItemsCount(obFieldsTree);
			delete removeItemsCount;
			delete getParentNodeByNameChain;
			return obFieldsTree;
	},
	
	jQueryPugins: {
			
	}
};

/*
SACID.jQueryPugins.settingsLinkTooltip = function() {
	//de('YES');
	//console.log(this);
}

de(SACID.jQueryPugins.settingsLinkTooltip());
*/

jQuery(document).ready(function() {
	for(jQPlg in SACID.jQueryPugins) {
		if(SACID.jQueryPugins[jQPlg].constructor == Function) {
			jQuery.fn[jQPlg] = SACID.jQueryPugins[jQPlg]
		}
	}
	SACID.urlHash.init();
	SACID.Components.init();
	//console.log(SACID.urlHash);

	jQuery(window).hashchange(function() {
		SACID.urlHash.onHashChange();
		SACID.Components.onHashChange();
	});
	jQuery(window).hashchange();
});

(function($, SACID) {
	
	
	
})(jQuery, SACID);

