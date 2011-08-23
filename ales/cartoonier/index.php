<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
	  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	  <script src="http://cartoonbank.ru/ales/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script> 

  <style type="text/css">

    #title { width: 1200px; height: 70px;}
    #store { width: 200px; height: 1000px; border:1px solid silver;float:left;}
    #canvas { width:800px; height:1000px; background-color:#EEEEEE;  border:1px solid silver;float:left;}
	.element  {}
	.element2  {border:1px dotted yellow;}
	#add_text {
		font-family: Arial;
		size:2em;
		color:#66CCFF;
		width: 200px; 
		height: 50px; 
		text-align: center; 
		float: left;
		margin-bottom:20px;
	}

  </style>
  <script>
  $(document).ready(function() {

	//updateFrames();

    $(".element").draggable({
        grid:[50,20],
        helper:'clone',
        opacity:0.3,
        revert:'invalid',
        scroll:true,
		cursor:'pointer'
    });

    $(".element").dblclick(deleteImage);
/*
	$('.edit').editable('', {
	 indicator : 'Сохраняю...',
	 tooltip   : 'Нажмите для редактирования...'
	});

	$('.add_text').editable({ 
			 type      : 'textarea',
			 cancel    : 'Cancel',
			 submit    : 'OK',
			 tooltip   : 'Нажмите для редактирования...'
	 });

*/

	$('.add_text').editable();

	$('#canvas').droppable({
        accept:'.element, .add_text',
        tolerance:'fit',
        hoverClass:"element2",
        drop:function(event, ui){
            var t = $(ui.draggable).attr("id");
            var elm = null;
            if (t==undefined)
                elm = $(ui.helper);
            else {
                elm = $(ui.helper).clone();
				elm.dblclick(deleteImage);
	            }
            elm.css("opacity", "1"); 
            $(this).append(elm);
            elm.draggable({
                grid:[50,20],
                opacity:0.3,
                revert:'invalid',  
                scroll:true
            });  
        }
		}); 


	//setDraggableAndDroppable();

     });
 /*
	 function updateFrames() {
		
		$("#canvas").empty();
		$(".frame_form").each(setFrame);
		
		}
*/
	function deleteImage() {
		var $image = $("#canvas.element[id='" + $(this).attr("id") + "']");
		$(this).remove();
		}
/*
	function deleteText() {
		var $text_form = $(".text_form[id='" + $(this).attr("id") + "']");
		$(this).remove();
		$text_form.find("input[id$=DELETE]").attr("checked", "checked");
		}

	function addTextField(text, frame) {
		var $new_form = $("#empty_text_form").clone(true);
		var form_count = $(".text_form").length;
		$(text).attr("id", form_count);
		$("#id_text_formset-TOTAL_FORMS").val(form_count + 1);
		$("#text_forms").append($new_form);
		$new_form.addClass("text_form").attr("id", form_count);
		$new_form.html($new_form.html().replace(/__prefix__/g, form_count));
		$new_form.find("input[id$=frame]").val($(frame).attr("id"));
		$new_form.find("input[id$=text]").val($(text).html());
		
		var top = Math.round($(text).position().top);
		var left = Math.round($(text).position().left);
		$new_form.find("input[id$=x]").val(top);
		$new_form.find("input[id$=y]").val(left);
		$new_form.find("input[id$=font_size]").val('12');
		}
	 
	function updateTextField(text) {
		var $text_form = $(".text_form[id='" + $(text).attr("id") + "']");
		$text_form.find("input[id$=x]").val($(text).position().top);
		$text_form.find("input[id$=y]").val($(text).position().left);
		var text_value = $(text).html().replace(/<br>/g, '\n');
		text_value = text_value.replace(/\n\n/g, '\n');
		$text_form.find("textarea[id$=text]").html(text_value);
		$text_form.find("input[id$=font_size]").val('12');
		}

		function setDroppable(frame) {
			$(frame).droppable({
				activeClass: "ui-state-default",
				hoverClass: "ui-state-hover",
				accept: ":not(.ui-sortable-helper)",
				drop: function( event, ui ) {
					if ($(ui.draggable).hasClass("dropped_image")) {
						updateImageField(ui.draggable);
					}
					else if ($(ui.draggable).hasClass("dropped_text")) {
						updateTextField(ui.draggable);
					}
					else if ($(ui.draggable).html() == "Click to edit") {
						var new_text = $( "<div class='dropped_text comic_text'></div>" );
						$(new_text).css({ 'left': ui.position.left - $(this).position().left - 30, 'top': ui.position.top - $(this).position().top });
						$(new_text).appendTo( this );
						$(new_text)
							.draggable({ containment: 'parent' })
							.text("Click to edit")
							.editable({'type': 'textarea', 'onEdit': startEditing, 'onSubmit': finishedEditing})
							.dblclick(deleteText);                
						addTextField(new_text, this);
					}
					else {
						var new_image = $( "<img class='dropped_image comic_image'></img>" );
						$(new_image).css({ 'left': ui.position.left - $(this).position().left - 30, 'top': ui.position.top - $(this).position().top - 20 });			    
						$(new_image).appendTo( this );
						$(new_image)
							.draggable({ containment: 'parent' })
							.attr("src", "/image/" + ui.draggable.attr("id") )
							.dblclick(deleteImage)
							.attr("id", ui.draggable.attr("id"));
						var frame = this;
						$(new_image).load(function() {
							addImageField(new_image, frame);
						});
					}
				}
			});
		}

	function setDraggableAndDroppable() {
		$( ".available_image" ).draggable({
			appendTo: "body",
			helper: "clone"
		});
		$( "#add_text" ).draggable({
			appendTo: "body",
			helper: "clone"
		});
		$(".comic_text")
			.draggable({ containment: 'canvas' })
			.dblclick(deleteText)
			.editable({'type': 'textarea', 'onEdit': startEditing, 'onSubmit': finishedEditing});
		
		setDroppable($(".frame"));
		}

	function startEditing(content) {
		$edit_box = $("textarea", this);
		$edit_box.val($edit_box.val().replace(/<br>/g, '\n'));
		$edit_box.val($edit_box.val().replace(/\n\n/g, '\n'));
		}
	 
	function finishedEditing(content) {
		updateTextField(this);
		$(this).html($(this).html().replace(/\n/g, '<br>'));
		}

	function addFrame() {
		var index = $(".frame_form").length;
		var $new_form = $("#empty_frame_form").clone(true);
		$new_form.addClass("frame_form").attr("id", "form_" + index);
		$("#frame_forms").append($new_form);
		$("#id_frame_formset-TOTAL_FORMS").val(index + 1);
		$new_form.html($new_form.html().replace(/__prefix__/g, index));
		setFrame(index, $new_form);
		}

	function setFrame(index, frame) {
		var $new_frame = $("<div class='frame'>");
		if ($(".frame", "#output_container").length % 2 != 0) {
			$new_frame.attr("id", index).css("width", "0px").animate({"width": "300px"});
		}    
		else {
			$new_frame.attr("id", index).css("height", "0px").animate({"height": "250px"});    
		}
		$("#output_container").append($new_frame);        
		$(frame).find("input[id$=order]").val(index);
		setDroppable($new_frame);
		}
*/
  </script>
</head>
<body style="">
<div id="title"><h2>Создайте свою карикатуру</h2>Перетяните картинку из коллекции слева направо. Позиционируйте. Добавьте следующую. Для удаления кликните два раза на персонаже.</div>
<div id="store">
	<div id="add_text" class="add_text element editable">Добавьте текст</div>
	<div id="sh_man_left_1" class="element"><img src="media/img/sh_man_left_1.png" width='60%'></div>
	<div id="sh_man_left_2" class="element"><img src="media/img/sh_man_right_1.png" width='60%'></div>
	<div id="baloon_1" class="element"><img src="media/img/baloon_1.png" width='60%'></div>
	<div id="baloon_2" class="element"><img src="media/img/baloon_2.png" width='60%'></div>
</div>  
<div id="canvas">
	<div id="droppable"></div>
</div>  
<div style="clear: both;"></div>





</body>
</html>