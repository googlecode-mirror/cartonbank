<?
// handle ajax request
niceHTML();

?>
<!doctype html>
<html>
 <head>
  <meta charset="utf-8" />
  <meta name="description" content="Карикатуры для газет, журналов и электронных СМИ. Лицензии." />
  <meta name="keywords" content=" картунбанк, cartoonbank, карикатуры, смешные, картинки, комиксы, карикатура, шарж, caricature, cartoon, comics" />
  <title> Поиск карикатур </title>
      
  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script src="http://search.cartoonbank.ru/js/jquery.colorbox.js"></script>
  <link rel="stylesheet" type="text/css" href="http://search.cartoonbank.ru/css/styles.css">
  <link rel="stylesheet" type="text/css" href="http://search.cartoonbank.ru/css/colorbox.css" />
 </head>
 <body>
<div id=main>
    <a href="http://cartoonbank.ru"><img src="http://cartoonbank.ru/img/cb-logo.png" style="border:none;" alt="Cartoonbank" width="514" height="44"></a>
    <div id=container>
        <div id=slide><h3><a href=http://search.cartoonbank.ru/>Очистить поиск</a></h3></div>
        <div id=search>
            <input id=s type=text placeholder="введите поисковое слово...">
        </div>
        <div id=stat><p>Наберите поисковое слово и нажмите клавишу Enter. Для подгрузки дополнительных результатов поиска нажмите кнопку "Ещё" под иконками. Для дополнительной фильтрации результатов поиска выберите одного из авторов. Выберите один из популярных тэгов справа для нового поиска.</p><p>Нажмите иконку для просмотра рисунка. Перемещайтесь между рисунками с помощью клавиш "стрелка влево" и "стрелка вправо" или с помощью щелка левой кнопки мыши.</p></div>
        <div id=thumbs></div>
        <div id=more></div>
    </div>
</div>
<div id=sidebar><div id=artists>
<!--<select id="authors" class=brands_sel onchange="if(!options[selectedIndex].defaultSelected)offset=0;searchterm('',options[selectedIndex].value);">-->
<select id="authors" class=brands_sel onchange="clear_search_results();set_url_hash();">
<option selected="" value="0" class=brands_sel>Все авторы</option><option value="3" class=brands_sel>Александров Василий</option><option value="8" class=brands_sel>Алёшин Игорь</option><option value="26" class=brands_sel>Анчуков Иван</option><option value="40" class=brands_sel>Батов Антон</option><option value="48" class=brands_sel>Белозёров Сергей</option><option value="24" class=brands_sel>Бибишев Вячеслав</option><option value="6" class=brands_sel>Богорад Виктор</option><option value="22" class=brands_sel>Бондаренко Дмитрий</option><option value="45" class=brands_sel>Бондаренко Марина</option><option value="38" class=brands_sel>Валиахметов Марат</option><option value="23" class=brands_sel>Гурский Аркадий</option><option value="49" class=brands_sel>Гуцол Олег</option><option value="66" class=brands_sel>Далпонте Паоло</option><option value="18" class=brands_sel>Дергачёв Олег</option><option value="54" class=brands_sel>Дружинин Валентин</option><option value="30" class=brands_sel>Дубинин Валентин</option><option value="27" class=brands_sel>Дубовский Александр</option><option value="41" class=brands_sel>Егоров Александр</option><option value="21" class=brands_sel>Ёлкин Сергей</option><option value="64" class=brands_sel>Зеленченко Татьяна</option><option value="36" class=brands_sel>Иванов Владимир</option><option value="25" class=brands_sel>Иорш Алексей</option><option value="50" class=brands_sel>Казаневский Владимир</option><option value="15" class=brands_sel>Камаев Владимир</option><option value="65" class=brands_sel>Капуста Николай</option><option value="5" class=brands_sel>Кийко Игорь</option><option value="67" class=brands_sel>Кинчаров Николай</option><option value="39" class=brands_sel>Кокарев Сергей</option><option value="47" class=brands_sel>Колгарёв Игорь</option><option value="32" class=brands_sel>Кононов Дмитрий</option><option value="20" class=brands_sel>Копельницкий Игорь</option><option value="56" class=brands_sel>Кустовский Алексей</option><option value="2" class=brands_sel>Лемехов Сергей</option><option value="69" class=brands_sel>Локтев Олег</option><option value="42" class=brands_sel>Лукьянченко Игорь</option><option value="19" class=brands_sel>Майстренко Дмитрий</option><option value="14" class=brands_sel>Максименко Ирина</option><option value="7" class=brands_sel>Мельник Леонид</option><option value="46" class=brands_sel>Москин Дмитрий</option><option value="53" class=brands_sel>Ненашев Владимир</option><option value="43" class=brands_sel>Никитин Игорь</option><option value="33" class=brands_sel>Новосёлов Валерий</option><option value="12" class=brands_sel>Осипов Евгений</option><option value="55" class=brands_sel>Пащенко Игорь</option><option value="44" class=brands_sel>Подвицкий Виталий</option><option value="31" class=brands_sel>Попов Александр</option><option value="9" class=brands_sel>Попов Андрей</option><option value="57" class=brands_sel>Репьёв Сергей</option><option value="11" class=brands_sel>Сергеев Александр</option><option value="4" class=brands_sel>Смагин Максим</option><option value="68" class=brands_sel>Смаль Олег</option><option value="37" class=brands_sel>Соколов Сергей</option><option value="59" class=brands_sel>Солдатов Владимир</option><option value="29" class=brands_sel>Степанов Владимир</option><option value="52" class=brands_sel>Сыченко Сергей</option><option value="16" class=brands_sel>Тарасенко Валерий</option><option value="63" class=brands_sel>Туровская Марина</option><option value="51" class=brands_sel>Фельдштейн Андрей</option><option value="62" class=brands_sel>Хомяков Валерий</option><option value="35" class=brands_sel>Цыганков Борис</option><option value="1" class=brands_sel>Шилов Вячеслав</option><option value="34" class=brands_sel>Шмидт Александр</option><option value="17" class=brands_sel>Эренбург Борис</option><option value="61" class=brands_sel>Яковлев Александр</option></select>
</div><div id=tags></div></div>

    <script>
    
    $( document ).ready(function() {
     // onload
     var brand = 0;
     var term = '';
     var sort = 0;
     var limit = 4;
     var offset = 0;
     var total_found = 0;
     var data = {brand:brand,term:term,sort:sort,limit:limit,offset:offset};
     
        // bind term input Enter event
        $('#s').on('keypress',function(e){
            if (!e) e = window.event;
            var keyCode = e.keyCode || e.which;
            if (keyCode == '13'){
              // Enter pressed
              
              // check term length
              if ($('#s').val().length<3){
                  alert ("Используйте для поиска слова, состоящие не менее, чем из трёх букв (цифр).");
                  return false;
              }
              
              clear_search_results(); // new search
              set_url_hash();
              //searchterm($("#s").val());
              return false;
            }
        });
        
        // bind hash changes
        $(window).on('hashchange', function() {
          parse_hash();
        });
        
        
      
    });
    /// onload
    </script>
    
    <script>
    function parse_hash(){
        var vars = location.hash.substring(1).split('&');
        var key = {};
        var s,b;
        for (i=0; i<vars.length; i++) {
          var tmp = vars[i].split('=');
          key[tmp[0]] = tmp[1];
        }
        if(typeof key['s'] == 'undefined'){
            s = ''; 
        }
        else{
            s = decodeURI(key['s'].trim());
        }
        if(typeof key['b'] == 'undefined'){
            b = 0;
        }
        else{
            b = key['b'].trim();
        }
        searchterm(s,b);
        return false;
    }
    
    function brand(id){
        ajaxreq();
        window.location.hash = "!l=l&b="+id;
        return false;
    }
        
    function searchterm(s,b){
        if (typeof b !='undefined' && b.length==0){
            brand = $("#authors").val();    
        }
        else{
            brand = b;
        }
        if (typeof brand == 'undefined'){
            brand=0;
        }
        
        if (typeof s !='undefined' && s.length==0){
            term = $("#s").val();
        }
        else{
            term = s.trim();
        }
        ajaxreq();
        
        $("#authors").val(brand);
        $("#s").val(decodeURI(s));
        return false;
    }
    
    function ajaxreq(){
        if (typeof brand == 'undefined'){brand='0';}
        if (typeof term == 'undefined'){term='';}
        if (typeof sort == 'undefined'){sort='0';}
        if (typeof limit == 'undefined'){limit='4';}
        if (typeof offset == 'undefined'){offset='0';}
        data = {brand:brand,term:term,sort:sort,limit:limit,offset:offset};
        $.ajax({
            url: 'http://search.cartoonbank.ru/conf/response.php',
            data: JSON.stringify(data),          
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            
            // display response
            success: function (result) {
                var path = 'http://th.cartoonbank.ru/'
                var items = [];
                var tags = [];
                var tagsstring = '';
                var res = result['images'];
                  $.each( res, function( key, img ) {
                    items.push( "<li id='" + img.id + "'><a class='th' title='"+img.artist+": <b>"+img.name+"</b> <a href=http://cartoonbank.ru/cartoon/"+img.id+"/ target=_blank>купить</a> "+"' href='http://sl.cartoonbank.ru/" + img.image + "'><img src='" + path + img.image + "' width=140 height=140></a></li>" );
                    tags.push(img.tags);
                    tagsstring = tagsstring + ", " + img.tags;
                  });
                  //$("#thumbs").html(items.join( "" )).addClass("imageslist");
                  $("#thumbs").append(items.join( "" )).addClass("imageslist");
                  
                  // stat 
                  // total found
                  total_found = result['total'][0]["total"];
                  limit = result['total'][0]["limit"];
                  offset = result['total'][0]["offset"];
                  var stat_message = '';
                  offset = parseInt(offset)+parseInt(limit);
                   
                  display_messages();

                  
                  // bind click to items
                  $(".th").colorbox({rel:'th'});

                 // output tags
                 if(typeof tagsstring != 'undefined'){
                    tags_outp(tagsstring);
                 }
                         
            },
            error: function(e){
                $("#stat").highlight();
                $("#stat").html("error: " + e.responseText);
            }
        });
        
    }

    function display_messages(){
        if (offset<total_found){
          stat_message = "Показаны "+offset+ " " + nouns(offset) + " из "+total_found;
          //more_message = "<a href='#' onclick='ajaxreq();return false;'>ещё</a>";
          more_message = "<button class=more onclick='ajaxreq();return false;'>ещё</button>";
        }
        else{
          stat_message = "Всего найдено: " + total_found + " " + nouns(total_found);
          more_message = "";
        }
        $("#stat").html(stat_message);
        $("#stat").highlight();
        $("#more").html(more_message);
    }
    
    function tags_outp(tags){
        tags = tags.split(', ');
        tags = sortByFrequencyAndFilter(tags);
        tags = tags.join("");
        $("#tags").html(tags);
        $(".tag").on("click",function(){
            term = this.innerHTML.trim();
            clear_search_results();
            $("#s").val(term);
            set_url_hash(term);
        });
        
    }
    
    function clear_search_results(){
        offset = 0;
        $('#thumbs').empty();    
        $('#stat').empty();
        $("#more").empty();
    }
    
    function set_url_hash(s){
        if (typeof s == 'undefined'){
            //s = '';
            s = $('#s').val();
        }
        window.location.hash = "!l=l&s="+s.replace(/\s/g,"%20")+"&b="+$("#authors").val();
    }
    
    function sortByFrequencyAndFilter(myArray)
    {
        var newArray = [];
        var freq = {};

        //Count Frequency of Occurances
        var i=myArray.length-1;
        for (var i;i>-1;i--)
        {
            var value = myArray[i];
            freq[value]==null?freq[value]=1:freq[value]++;
        }

        //Create Array of Filtered Values
        for (var value in freq)
        {
            newArray.push("<span class=tag>"+value+"</span> ");
        }

        //Define Sort Function and Return Sorted Results
        function compareFreq(a,b)
        {
            return freq[b]-freq[a];
        }

        return newArray.sort(compareFreq);
    }    
    
    function nouns(number){
        switch (parseInt(number))
        {
        case 1:
            $text = "карикатура";
            break;
        case 2:
        case 3:
        case 4:
            $text = "карикатуры";
            break;
        default:
            $text = "карикатур";
            break;
        }

        if (number>=10 && number<=19){$text = "карикатур";}
        return $text;
    }
    
    jQuery.fn.highlight = function() {
        $(this).each(function() {
            var el = $(this);
            el.before("<div/>")
            el.prev()
                .width(el.width())
                .height(el.height())
                .css({
                    "position": "absolute",
                    "background-color": "#ffff99",
                    "opacity": ".9"   
                })
                .fadeOut(700);
        });
    }
    </script>
 
 </body>
</html>
<?
function niceHTML(){
    if (isset($_GET['_escaped_fragment_'])){
        $my_path = dirname(__FILE__);
        include ($my_path.'/conf/responsehtml.php');
        exit;
    }
    return false;
}
?>

    