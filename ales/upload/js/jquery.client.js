$(document).ready(function(){


	// Стандарный input для файлов
	var fileInput = $('#fileUpload0'); 
	
	
	 // Проверка поддержки File API в браузере  и DnD
    if(window.FileReader == null || !('draggable' in document.createElement('span')))
	
	/* если file api не поддерживается, реализуем загрузку файлов постаринке */
	{
		var	result = true; // флаг, который нам поможет засубмитить форму после того как все фотки будут отправленны (используем этот флаг чтобы не вводить дополнителную переменную) 
		
		/*делаем действия для "отставших браузеров"*/
		fileInput
			.attr("name","fileUpload0")
			.after('<div><input type="button" id="addFile" value="+"></div>');
		
		var fileCounter = 0;
		
		$("#addFile").click(
		function() {
			var newInput = $("#fileUpload"+fileCounter);
			
			fileCounter++;
			$("#addFile").parent().before('<div><input type="file" name="fileUpload"'+fileCounter+' id="fileUpload"'+fileCounter+'/></div>');
		});
		
					
    }
	/* end если file api не поддерживается */
	
	
	/* если поддерживется file api и DnD */
	else {
		/* динамически создаем компоненты для drag and drop */
		fileInput.after('<div id="img-container"><div class="text">Перетащите сюда файл(ы)</div><ul id="img-list"></ul></div>');
		
    
    	// ul-список, содержащий миниатюрки выбранных файлов
    	var imgList = $('ul#img-list'),
    
   		// Контейнер, куда можно помещать файлы методом drag and drop
    	dropBox = $('#img-container'),

    	// Счетчик всех выбранных файлов и их размера
    	imgCount = 0,
    	imgSize = 0,
		uploadedCount = 0,
		
		result = false;
		
	
	/* 
		обработчики событий интрфейса
	*/
	
		// удаление выбранного фото
	$(".delpreview").live("click", function(){
											  
			$(this).parent().parent().remove();

	});

    // Обновление progress bar'а
    function updateProgress(bar, value) {
        var width = bar.width();
        var bgrValue = -width + (value * (width / 100));
        bar.attr('rel', value).css('background-position', bgrValue+'px center').text(value+'%');
    }

    // Отображение выбраных файлов и создание миниатюр
    function displayFiles(files) {
        var imageType = /image.*/;
        var num = 0;
        
        $.each(files, function(i, file) {
					
			/*в ФФ возникает проблема при выгрузке файлов кириллицей, так что проверяем есть ли кириллические символы*/		
					
			if ( /[а-я]/i.test(file.name) ) {
				
				alert('название файлов должно быть только латинницей');
				/* или можно заменbnm русcкие буквы транслитом */
			}
			else
			{
				// Отсеиваем не картинки
				if (!file.type.match(imageType)) {
					alert('загрузить можно только изображения');
					return true;
				}
				
				num++;
				
				// Создаем элемент li и помещаем в него название, миниатюру и progress bar, 
				// а также создаем ему свойство file, куда помещаем объект File (при загрузке понадобится)
				var li = $('<li/>').appendTo(imgList);

				var imgcont = $('<div class="imgcontainer" />').appendTo(li);



				var img = $('<img/>').appendTo(imgcont);

	var descript = $('<div class="descript">    <div><label class="lbl" for="carname">название</label><input type="text" id="carname" name="carname" value="название '+Math.random()+'" class="iname" /></div><div><label class="lbl" for="cardescription">описание</label><textarea rows="3" id="cardescription" name="cardescription" class="idescription">описание '+Math.random()+' </textarea></div><div><label class="lbl" for="cartags">ключевые слова</label><textarea rows="4" id="cartags" name="cartags" class="itags">тэги '+Math.random()+'</textarea></div>           <div>   <input id="colored'+num+'" type="checkbox" name="colored" checked="checked" class="chbcolored"/><label for="colored'+num+'" class="coloredchb">цветной</label>   <input id="tema'+num+'" type="checkbox" name="tema" class="chbtema"/><label for="tema'+num+'" class="temachb">тема дня</label>      </div>                </div> <div class="descriptr"> <input type="radio" id="category4'+num+'" name="category'+num+'" value="4"><label for="category4'+num+'">Карикатура</label><br><input type="radio" id="category5'+num+'" name="category'+num+'" value="5" checked="checked"><label for="category5'+num+'">Cartoon</label><br><input type="radio" id="category6'+num+'" name="category'+num+'" value="6"><label for="category6'+num+'">Artoon</label><br><input type="radio" id="category11'+num+'" name="category'+num+'" value="11"><label for="category11'+num+'">Разное</label><br><input type="radio" id="category13'+num+'" name="category'+num+'" value="13"><label for="category13'+num+'">Коллаж</label><br><input type="radio" id="category14'+num+'" name="category'+num+'" value="14"><label for="category14'+num+'">Шарж</label><br><input type="radio" id="category15'+num+'" name="category'+num+'" value="15"><label for="category15'+num+'">Стрип</label><br><input type="radio" id="category666'+num+'" name="category'+num+'" value="666"><label for="category666'+num+'">Рабочий стол</label></div>').appendTo(li);

				$('<div/>').addClass('progress').attr('rel', '0').text('0%').appendTo(imgcont);
				$('<div/>').addClass('clearall').appendTo(li);


				$('<span class="delpreview">X</span>').appendTo(imgcont);
				$('<div class="photoName"/>').text(file.name).appendTo(imgcont);
				$('<div class="photoName" />').text(num).appendTo(imgcont);


				li.get(0).file = file;

				
				// Создаем объект FileReader и по завершении чтения файла, отображаем миниатюру и обновляем
				// инфу обо всех файлах
				var reader = new FileReader();
				reader.onload = (function(aImg) {
					return function(e) {
						aImg.attr('src', e.target.result);
						aImg.attr('width', 140);
						imgCount++;
						imgSize += file.size;
					};
				})(img);
				
				reader.readAsDataURL(file);
			}
        });
    }
   
    ////////////////////////////////////////////////////////////////////////////


    // Обработка события выбора файлов через стандартный input
    // (при вызове обработчика в свойстве files элемента input содержится объект FileList,
    //  содержащий выбранные файлы)
    fileInput.bind({
        change: function() {
            displayFiles(this.files);
        }
    });

    // Обработка событий drag and drop при перетаскивании файлов на элемент dropBox
    // (когда файлы бросят на принимающий элемент событию drop передается объект Event,
    //  который содержит информацию о файлах в свойстве dataTransfer.files. В jQuery "оригинал"
    //  объекта-события передается в св-ве originalEvent)
    dropBox.bind({
        dragenter: function() {
            $(this).addClass('highlighted');
            return false;
        },
        dragover: function() {
            return false;
        },
        dragleave: function() {
            $(this).removeClass('highlighted');
            return false;
        },
        drop: function(e) {
            var dt = e.originalEvent.dataTransfer;
            displayFiles(dt.files);
            return false;
        }
    });
		
		
		
	}
	/* end если поддерживется file api */
  
    
    ////////////////////////////////////////////////////////////////////////////




    // Обаботка события нажатия на кнопку "Загрузить". Проходим по всем миниатюрам из списка,
    // читаем у каждой свойство file (добавленное при создании) и начинаем загрузку, создавая
    // экземпляры объекта uploaderObject. По мере загрузки, обновляем показания progress bar,
    // через обработчик onprogress, по завершении выводим информацию
	
	/*
		переменная, которая когда становится 1 субмитит форму
	 	данная форма изначально отправляет картинки на сервер, а уже после того, очистив поле file, субмитит нашу форму

	  */
	var alldone = 0;
	
    $(".regForm").submit(function() {
        
		jQuery('#file-field').val('');
		
		
		if(imgList.find('li').length == 0)
		{
			result = true;	
		}
		else if(alldone == 1)
		{
			result = true;
		}
		else
		{
			result = false;	
		}
		
		if(result == false)
		{



//foreach 2
var valid = true;
imgList.find('li').each(function() {

    var uploadItem = this;
    if (checkEmptyFields(uploadItem, valid) == false) {
        $('.errormsg').html('<div class="alertmsg" style="clear:both;">Ошибки на странице</div>');
        valid = false;
    }
}                 
);
if (!valid){
    return false;
}


			imgList.find('li').each(function() {
	
				var uploadItem = this;
				
				if (checkEmptyFields(uploadItem, valid) == false) {
					$('.errormsg').html('<div class="alertmsg" style="clear:both;">Ошибки на странице</div>');
					valid = false;
				    return false;
				}
                
                
                if (valid){




                    var pBar = $(uploadItem).find('.progress');

                    new uploaderObject({
                        file:       uploadItem.file,

                        /*переменная ulr - адрес скрипта, который будет принимать фото со стороны сервера (в моём случае это значение action нашей формы)*/

                        //url:        $(".regForm").attr('action'),
                        url:        "http://109.120.143.27/cb/ales/upload/savefiles.php",
                        fieldName:  'my-pic',
                        carName:		$(uploadItem).find('#carname').val(),
                        carDescription:	$(uploadItem).find('#cardescription').val(),
                        carTags:		$(uploadItem).find('#cartags').val(),
                        carCategory:	$(uploadItem).find('.descriptr').find('input:checked').val(),
                        carColor:		$(uploadItem).find('.descript').find('.chbcolored:checked').val(),
                        carTema:		$(uploadItem).find('.descript').find('.chbtema:checked').val(),
                        //$(uploadItem).find('#colored1')
                        //<input id="colored1" type="checkbox" name="colored" checked="checked" class="chbcolored">
                        //$(uploadItem).find('.descript').find('.chbcolored').val()
                        //$(uploadItem).find('.descript').find('.chbcolored:checked').val()
                        onprogress: function(percents) {
                            updateProgress(pBar, percents);
                        },

                        oncomplete: function(done, data) {
                            if(done) {
                                updateProgress(pBar, 100);
                                uploadedCount++;
                                if(uploadedCount == jQuery('#img-list li').length)
                                    {
                                    alldone = 1;
                                    result = true;
                                    $(".regForm").submit();
                                }
                            } else {

                            }
                        }
                    });

                }//if valid
                
        	}


  

			);//imgList.find('li').each
		}//if(result == false)
        
///foreach2    
/*
                        if(uploadedCount == jQuery('#img-list li').length)
                            {
                                alldone = 1;
                                result = true;
                                $(".regForm").submit();
                            }
*/                            
		return result;
    });

});

/*
 * Объект-загрузчик файла на сервер.
 * Передаваемые параметры:
 * file       - объект File (обязателен)
 * url        - строка, указывает куда загружать (обязателен)
 * fieldName  - имя поля, содержащего файл (как если задать атрибут name тегу input)
 * carName	- cartoon name
 * onprogress - функция обратного вызова, вызывается при обновлении данных
 *              о процессе загрузки, принимает один параметр: состояние загрузки (в процентах)
 * oncomplete - функция обратного вызова, вызывается при завершении загрузки, принимает два параметра:
 *              uploaded - содержит true, в случае успеха и false, если возникли какие-либо ошибки;
 *              data - в случае успеха в него передается ответ сервера
 *              
 *              если в процессе загрузки возникли ошибки, то в свойство lastError объекта помещается
 *              объект ошибки, содержащий два поля: code и text
 */

var uploaderObject = function(params) {

    if(!params.file || !params.url) {
        return false;
    }


    this.xhr = new XMLHttpRequest();
    this.reader = new FileReader();

    this.progress = 0;
    this.uploaded = false;
    this.successful = false;
    this.lastError = false;
    
    var self = this;    

    self.reader.onload = function() {
        self.xhr.upload.addEventListener("progress", function(e) {
            if (e.lengthComputable) {
                self.progress = (e.loaded * 100) / e.total;
                if(params.onprogress instanceof Function) {
                    params.onprogress.call(self, Math.round(self.progress));
                }
            }
        }, false);

        self.xhr.upload.addEventListener("load", function(){
            self.progress = 100;
            self.uploaded = true;
        }, false);

        self.xhr.upload.addEventListener("error", function(){            
            self.lastError = {
                code: 1,
                text: 'Error uploading on server'
            };
        }, false);

        self.xhr.onreadystatechange = function () {
            var callbackDefined = params.oncomplete instanceof Function;
            if (this.readyState == 4) {
                if(this.status == 200) {
                    if(!self.uploaded) {
                        if(callbackDefined) {
                            params.oncomplete.call(self, false);
                        }
                    } else {
                        self.successful = true;
                        if(callbackDefined) {
                            params.oncomplete.call(self, true, this.responseText);
                        }
                    }
                } else {
                    self.lastError = {
                        code: this.status,
                        text: 'HTTP response code is not OK ('+this.status+')'
                    };
                    if(callbackDefined) {
                        params.oncomplete.call(self, false);
                    }
                }
            }
        };

        self.xhr.open("POST", params.url);

        var boundary = "----------";
		boundary += Math.floor(Math.random()*32768);
		boundary += Math.floor(Math.random()*32768);
		boundary += Math.floor(Math.random()*32768);
        imageboundary = Math.floor(Math.random()*32768);

        self.xhr.setRequestHeader("Content-Type", "multipart/form-data; boundary="+boundary);
        //self.xhr.setRequestHeader("Content-Type", "multipart/mixed; boundary="+boundary);
        self.xhr.setRequestHeader("Cache-Control", "no-cache");
         
		var body = "--" + boundary + "\r\n";

		body += "Content-Disposition: form-data; name=\"carname\"\r\n";
        body += "Content-Type:text/plain\r\n\r\n";
        body += params.carName + "\r\n"; 
        body += "--" + boundary + "\r\n";

		body += "Content-Disposition: form-data; name=\"cardescription\"\r\n";
        body += "Content-Type:text/plain\r\n\r\n";
        body += params.carDescription + "\r\n"; 
        body += "--" + boundary + "\r\n";

		body += "Content-Disposition: form-data; name=\"cartags\"\r\n";
        body += "Content-Type:text/plain\r\n\r\n";
        body += params.carTags + "\r\n"; 
        body += "--" + boundary + "\r\n";

		body += "Content-Disposition: form-data; name=\"carcategory\"\r\n";
        body += "Content-Type:text/plain\r\n\r\n";
        body += params.carCategory + "\r\n"; 
        body += "--" + boundary + "\r\n";

		body += "Content-Disposition: form-data; name=\"colored\"\r\n";
        body += "Content-Type:text/plain\r\n\r\n";
        body += params.carColor + "\r\n"; 
        body += "--" + boundary + "\r\n";

		body += "Content-Disposition: form-data; name=\"tema\"\r\n";
        body += "Content-Type:text/plain\r\n\r\n";
        body += params.carTema + "\r\n"; 
        body += "--" + boundary + "\r\n";

        body += "Content-Disposition: form-data; name=\"submit_action\"\r\n";
        body += "Content-Type:text/plain\r\n\r\n";
        body += "add\r\n"; 
        body += "--" + boundary + "\r\n";

        body += "--" + imageboundary + "\r\n";
		body += "Content-Disposition: file; name='"+(params.fieldName || 'file')+"'; filename='" + params.file.name + "'\r\n";
        body += "Content-Type: application/octet-stream\r\n";
        //body += "Content-Type: image/jpeg\r\n";
        body += "Content-Transfer-Encoding: base64\r\n\r\n";
        
        body += self.reader.result.substr(self.reader.result.indexOf("base64,",0)+7) + "\r\n";
        //body += self.reader.result + "\r\n";
        
        body += "--" + imageboundary + "--\r\n";
        body += "--" + boundary + "--";

        if(self.xhr.sendAsBinary) {
            // firefox
            self.xhr.sendAsBinary(body);
        } else {
            // chrome (W3C spec.)
            self.xhr.send(body);
        }
    };
    //self.reader.readAsBinaryString(params.file);
    self.reader.readAsDataURL(params.file);
};

function checkEmptyFields(uploadItem, valid) {

	$(uploadItem).find('#carname').parent().removeClass("alertmsg");
	$(uploadItem).find('#cardescription').parent().removeClass("alertmsg");
	$(uploadItem).find('#cartags').parent().removeClass("alertmsg");

	if ($(uploadItem).find('#carname').val().length ==0) {
		$(uploadItem).find('#carname').parent().append('<div class="alertmsg"></div>');
		$(uploadItem).find('#carname').parent().addClass("alertmsg");
		valid = false;
	}
	if ($(uploadItem).find('#cardescription').val().length ==0) {
		$(uploadItem).find('#cardescription').parent().append('<div class="alertmsg"></div>');
		$(uploadItem).find('#cardescription').parent().addClass("alertmsg");
		valid = false;
	}
	else if ($(uploadItem).find('#cartags').val().length ==0) {
		$(uploadItem).find('#cartags').parent().append('<div class="alertmsg"></div>');
		$(uploadItem).find('#cartags').parent().addClass("alertmsg");
		valid = false;
	}

	return valid;	
}