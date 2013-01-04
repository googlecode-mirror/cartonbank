var colCount = 0;
var colWidth = 0;
var margin = 20;
var windowWidth = 0;
var blocks = [];

$(function(){
	$(window).resize(setupBlocks);
	$(window).ready(setupBlocks);
});

function setupBlocks() {
	windowWidth = $(window).width();
	colWidth = $('.block').outerWidth();
	blocks = [];
	//console.log(blocks);
	colCount = Math.floor(windowWidth/(colWidth+margin));
	for(var i=0;i<colCount;i++){
		blocks.push(margin);
	}
    loadBlockContent();
    positionBlocks();
    bindImagePopup();
    
}

function loadBlockContent() {
    $('.block').each(function(){
        var cid = $(this).attr("id").substr(3);
        var url = "http://cartoonbank.ru/ales/masonry/getblockcontent.php?cid="+cid;
		$(this).load(url);

    });
}

/*
function loadBlockContent() {
    $('.block').each(function(){
        var cid = $(this).attr("id").substr(3);
                $.ajax({
        url: "http://cartoonbank.ru/ales/masonry/getblockcontent.php?cid="+cid,
        success: function(html)
        {
            var htmlStr = 'test load';
            $(this).text(htmlStr);
            if(html)
            {
                $(this).html(html);
            }
            else
            {
                $(this).html("<center>can not load image</center>");
            }
        }
        });
    });
}
*/
function positionBlocks() {
	$('.block').each(function(){
		var min = Array.min(blocks);
		var index = $.inArray(min, blocks);
		var leftPos = margin+(index*(colWidth+margin));
		$(this).css({
			'left':leftPos+'px',
			'top':min+'px'
		});
        //$(this > ".icon").bind('click',function(){colorbox();});
		blocks[index] = min+$(this).outerHeight()+margin;
	});	
}



function bindImagePopup(){
    $("div.icon").click(function(){
        alert('ha');
        ///colorbox();
    });
    
    
    /*$(".icon").bind('click', function() {
      colorbox();
    });*/
}

function colorbox(){
    alert("colorbox");
}

// Function to get the Min value in Array
Array.min = function(array) {
    return Math.min.apply(Math, array);
};