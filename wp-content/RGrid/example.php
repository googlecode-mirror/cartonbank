<?php
    /**
    * o------------------------------------------------------------------------------o
    * | This package is licensed under the Phpguru license. A quick summary is       |
    * | that for commercial use, there is a small one-time licensing fee to pay. For |
    * | registered charities and educational institutes there is a reduced license   |
    * | fee available. You can read more  at:                                        |
    * |                                                                              |
    * |                  http://www.phpguru.org/static/license.html                  |
    * o------------------------------------------------------------------------------o
    *
    * © Copyright 2008,2009 Richard Heyes
    */

    /**
    * This an example script that uses the datagrid and shows it off
    */
    
    /**
    * Include the datagrid code 
    */
    require_once('RGrid.php');
    
    $params['hostname'] = 'localhost';
    $params['username'] = 'z58365_cbru3';
    $params['password'] = 'greenbat';
    $params['database'] = 'z58365_cbru3';

    $sql = "SELECT p.ID, l.image as image, l.name as title, b.name as author, p.votes, p.points, u.vote_date, u.ip  
		FROM	`wp_fsr_post` as p, 
				`wp_fsr_user` as u, 
				`wp_product_list` as l,
				`wp_product_brands` as b				
		WHERE p.id = u.post 
			AND p.id = l.id 
			AND l.brand = b.id
			ORDER BY u.vote_date DESC";

//$sql = "SELECT * FROM `wp_fsr_post` as p, `wp_fsr_user` as u WHERE p.id = u.post order by vote_date desc";

    /**
    * Create the datagrid with the connection parameters and SQL query
    * defined above. You MUST specify an ORDER BY
    * clause (with ASC/DESC direction indicator) - if not then ordering
    * will be fubar. I will eventually fix this so that the headers
    * aren't clickable if you don't supply an ORDER BY, for the time
    * being just specify an ORDER BY clause (the one you specify will
    * be used by default).
    */
    $grid = RGrid::Create($params, $sql);

    /**
    * Disable sorting
    */
    //$grid->allowSorting = false;
        
    /**
    * Turn the column headers off/on
    */
    $grid->showHeaders = true;
    
    /**
    * Different to the above - this is the HTML that is shown above the
    * actual datagrid
    */
    $grid->SetHeaderHTML('<div style="text-align: center">Таблица последних голосований</div>');

    /**
    * Sets nice(r) display names instead of the raw column names
    */
    $grid->SetDisplayNames(array('ID'       => 'номер',
                                 'votes'   => 'голосов',
                                 'points'    => 'очков',
                                 'user' => 'user',
                                 'vote_date'   => 'дата голосования',
                                 'post'   => 'post',
                                 'title'   => 'название',
                                 'image'   => 'иконка',
                                 'ip'    => 'ip'));

    /**
    * This hides a column from the result set. It would of course be easier and
    * faster just to not select it, but this isn't always possible.
    */
	$grid->HideColumn('ID');
                                 
    /**
    * This simply sets the specified columns not to be passed through htmlspecialchars()
    * Generally any column that shows HTML
    */
    $grid->NoSpecialChars('actions', 'ac_id');
    $grid->NoSpecialChars('ID', 'image');
    
    /**
    * A callback function that gets called for each and every row.
    * The callback function should take a single argument by reference,
    * which is an associative array of the row data. It's by reference
    * so that any changes you make will not be lost.
    */
    $grid->rowcallback = 'RowCallback';
    function RowCallback(&$row)
    {
		//pokazh($row);
		$row['image'] = "<a href='?page_id=29&cartoonid=".$row['ID']."'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$row['image']."' width='100'></a>";
		$row['ID'] = "<a href='?page_id=29&cartoonid=".$row['ID']."'>".$row['ID']."</a>";
		//$row['image'] = "<a href='?page_id=29&cartoonid=".$row["ID"]."'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$row['image']."' width='100'></a>";
    }
    
    /**
    * Here just for show. This function sets the number of rows to set per page.
    * The default is 20.
    */
    $grid->SetPerPage(10);
    
    /**
    * Again, this is purely here for show. It doesn't do anything, but simply shows
    * how you can use this function. If you change the perPage after you call
    * this function, then obviously the page count will change.
    */
    // echo '<p>Number of pages: ' . $grid->GetPageCount() . '<br />';
    
    /**
    * Again, here just for show. Gets the number of rows in the grid.
    */
    // echo 'Number of rows: ' . $grid->GetRowCount() . '</p>';
    
    /**
    * Gets the connection resource. Here just for show.
    */
    //echo 'The connection (var_dump()): ';
    //var_dump($grid->GetConnection());

    /**
    * Gets the result resource. Here just for show.
    */
    //echo '<br />The result resouce (var_dump()): ';
    //var_dump($grid->GetResultset());

    /**
    * The HTML. The appearance can be customised using CSS. Naturally you would (should) put
    * all of the datagrid styling in a central CSS file that can be <link>ed to by all of
    * your websites' pages. That way:
    *  o All of your datagrids will look the same
    *  o Changes to the appearance will affect all of your datagrids across your
    *    entire website. You might want this necessarily, but must do.
    *
    * eg. <link rel="stylesheet" type="text/css" media="screen" href="/css/datagrid.css" />
    */
?>
    <style type="text/css">
    <!--
        table.datagrid {
            font-family: Arial;
        }
        /**
        * Format the table headers
        */
        .datagrid thead th#header {
            background-color: #ddd;
        }

        .datagrid thead th {
            color: black;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 10pt;
            font-family: Verdana;
            text-align: left;
            padding-left: 5px;
        }
        
        .datagrid thead th#header {
            border: 0;
        }
        
        /**
        * This is here to illustrate how to format the alternative rows
        */
        .datagrid tbody td.altrow {
            background-color: #fff;
        }
        
        /**
        * Format the alternative columns with a light grey background
        */
        .datagrid tbody td.altcol
        {
          background-color: #eee;
        }
        
        /**
        * Format the actions column
        */
        .datagrid tbody td.col_4 a {
            text-decoration: none;
        }

        /**
        * Format the table headers
        */
        .datagrid tbody td {
            padding-left: 5px;
        }

        #   /**
        #* Format the mouseover
        #*/
        #.datagrid tbody td.mouseover {
        #    background-color: #FFB7B7;
        #}
        
        /**
        * No link underlining
        */
        .datagrid a {
            text-decoration: none;
        }
    // -->
    </style>

    <?php $grid->Display() ?>
    
    <?if(preg_match('/^\/datagrid/', $_SERVER['REQUEST_URI'])):?>
        <pre><?print_r($grid)?></pre>
    <?endif?>
