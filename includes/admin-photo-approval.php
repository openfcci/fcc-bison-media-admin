<?php
use Parse\ParseObject;
use Parse\ParseQuery;
/*************************** Photo Feed Admin Page *****************************
 *******************************************************************************
 * Photo feed admin page functions begin here.
 * (Continued from Options_Manager)
 */

  /**
   * ADMIN PAGE: Bison Media—Photo Approval
   * Get active page tab OR set unapproved_photos tab as a default tab.
   */
  if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = $_GET[ 'tab' ];
  }
  else {
    $active_tab = 'unapproved_photos' ;
  } //End Active Tab function

  /**
   * ADMIN PAGE: Bison Media—Photo Approval
   * If admin, set options manager/register menu and page tabs
   */
  if ( is_admin() ) {
      new Options_Manager( 'Bison Media Photo Approval Feed',
                           'Bison Media—Photo Approval',
                           'manage_options', //capabilities
                           'bison-media-photo-admin',
                            //array of tabs on the page
                           array('Unapproved Photos', 'Approved Photos') //, 'Debug'
      );
  } //End Options_Manager

  class Options_Manager {

      private $page_title = '';
      private $menu_title = '';
      private $capability = '';
      private $menu_slug = '';
      private $tabs = array();

      /**
       * Construct Admin Menu Arguments Array
       */
      public function __construct( $page_title, $menu_title, $capability, $menu_slug, $tabs ) {
      	$this->page_title = $page_title;
      	$this->menu_title = $menu_title;
      	$this->capability = $capability;
      	$this->menu_slug = $menu_slug;
      	$this->tabs = $tabs;
      	add_action( 'admin_menu', array( $this, 'admin_menu' ) );
      } //End function __construct

      /**
       * Create options page
       */
      public function admin_menu() {
      	add_menu_page( $this->page_title,
                       $this->menu_title,
                       $this->capability,
                       $this->menu_slug,
                       array(  $this, 'options' ) );
      } //END function admin_menu

      /**
       * Give the page basic outline and print the form
       */
      public function options() {
      	echo '<div class="wrap">';
      	echo '<h2>' . $this->page_title . '</h2>';
      	try {
      	    $this->form();
      	} catch (Exception $e) {
      	    echo $e->getMessage();
      	}
      	echo '</div> <!-- end .wrap -->';
      } //END function options

      /**
       * Display the tabs and the form
       */
      public function form() {
      	if ( isset( $_GET[ 'tab' ] ) ) {
      	    $tab = $_GET[ 'tab' ];
      	} else {
      	    $tab = 0;
      	}
      	$this->options_tabs( $tab );
      	echo '<br>';
      	$this->content_tabs( $tab );
      } //END function form

      /**
       * Render the tabs
       */
      public function options_tabs ( $curr_tab ) {
      	echo '<h2 class="nav-tab-wrapper">';
      	foreach ( $this->tabs as $tab => $name ) {
      	    $class = ( $tab == $curr_tab ) ? 'nav-tab-active' : '';
      	    echo "<a class='nav-tab $class' href='?page=$this->menu_slug&tab=$tab'>$name</a>";
      	}
      	echo '</h2>';
      } //END function options_tabs

       /**
        * Photo Feed Admin Page Tabs
        */
        public function content_tabs ( $tab ) {

          /**
           * Tab 1: Unapproved Photos
           */
        	switch ( $tab ) {
            case '0':
             $photos = new ParseObject("Photos"); // set Parse Object to "Photos"
             $query = new ParseQuery("Photos");
             $query->EqualTo("approved", false);  //Filters to Unapproved Photos
             $query->descending("createdAt"); // Order by most recent uploads first

             $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
             $limit = 5;
             $offset = ( $pagenum - 1 ) * $limit;
             $parseData = $query->find();
             $entries = array_slice($parseData, $offset, $limit );
             $thickbox = add_thickbox();

             $total = count($parseData);
             $num_of_pages = ceil( $total / $limit );
             $page_links = paginate_links( array(
                 'base' => add_query_arg( 'pagenum', '%#%' ),
                 'format' => '',
                 'prev_text' => __( '&laquo;', 'aag' ),
                 'next_text' => __( '&raquo;', 'aag' ),
                 'total' => $num_of_pages,
                 'current' => $pagenum
             ) );


           echo '<div class="wrap">';
           ?>
           <table class="widefat" style="max-width: 480px;">
               <thead>
                   <tr>
                       <th scope="col" class="manage-column column-name" style="">Photo</th>
                       <th scope="col" class="manage-column column-name" style="">Submitted</th>
                       <th colspan="2" scope="col" class="manage-column column-name" style="text-align: center;"><span class="displaying-num">Unapproved Photos: <?php echo $total; ?></span></th>
                   </tr>
               </thead>

               <tfoot>
                   <tr>
                       <th scope="col" class="manage-column column-name" style="">Photo</th>
                       <th scope="col" class="manage-column column-name" style="">Submitted</th>
                      <th colspan="2" scope="col" class="manage-column column-name" style="text-align: center;"><span class="displaying-num">Unapproved Photos: <?php echo $total; ?></span></th>
                   </tr>
               </tfoot>

               <tbody>
                   <?php if( $entries ) { ?>

                       <?php
                       $count = 1;
                       $class = '';
                       foreach( $entries as $entry ) {
                           $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
                           $photoId = $entry->getObjectId();
                       ?>

                       <tr <?php echo $class; ?> >
                           <td><?php echo $thickbox ?><a href="<?php echo $entry->image->getURL(); ?>?TB_iframe=true&width=auto&height=auto" class="thickbox"><img src="<?php echo $entry->thumbnail->getURL() ?>"></a></td>
                           <td><?php echo $entry->getCreatedAt()->format('m/d/Y'); ?></td>
                           <td align="center" style="padding-top:0px;"><form id="parse_approve" name="parse_approve_form" method="post" method="post"><div><p><input type="hidden" name="ob_id" value="<?php echo $photoId; ?>"/></p></div><div><input type="submit" id="push_button" class="button-primary" name="parse_approve_push_btn" value="Approve" style="width:95px;"/></div></form></td>
                           <td align="center" style="padding-top:0px;"><form id="parse_delete" name="parse_delete_form" method="post" method="post"><div><p><input type="hidden" name="ob_id" value="<?php echo $photoId; ?>"/></p></div><div><input type="submit" id="push_button" class="button-primary" name="parse_delete_push_btn" value="Delete" style="background:#F26969; border-color:#D44040; width:75px;"/></div></form></td>
                       </tr>

                       <?php
                           $count++;
                       }
                       ?>

                   <?php } else { ?>
                   <tr>
                       <td colspan="2">No posts yet</td>
                   </tr>
                   <?php } ?>
               </tbody>
           </table>

           <?php

           if ( $page_links ) {
               echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0; float:left;">' . $page_links . '</div></div>';
           }

           echo '</div>';
            /** End Tab 1 */

        		break;
        	    case '1':
              /**
               * Tab 2: Approved Photos
               */

               $photos = new ParseObject("Photos"); // set Parse Object to "Photos"
               $query = new ParseQuery("Photos");
               $query->notEqualTo("approved", false);  //Filters to Approved Photos
               $query->descending("createdAt"); // Order by most recent uploads first

               $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
               $limit = 5;
               $offset = ( $pagenum - 1 ) * $limit;
               $parseData = $query->find();
               $entries = array_slice($parseData, $offset, $limit );
               $thickbox = add_thickbox();

               $total = count($parseData);
               $num_of_pages = ceil( $total / $limit );
               $page_links = paginate_links( array(
                   'base' => add_query_arg( 'pagenum', '%#%' ),
                   'format' => '',
                   'prev_text' => __( '&laquo;', 'aag' ),
                   'next_text' => __( '&raquo;', 'aag' ),
                   'total' => $num_of_pages,
                   'current' => $pagenum
               ) );

               echo '<div class="wrap">';

               ?>

               <table class="widefat" style="max-width: 480px;">
                   <thead>
                       <tr>
                           <th scope="col" class="manage-column column-name" style="">Photo</th>
                           <th scope="col" class="manage-column column-name" style="">Submitted</th>
                           <th colspan="2" scope="col" class="manage-column column-name" style="text-align: center;"><span class="displaying-num">Approved Photos: <?php echo $total; ?></span></th>

                       </tr>
                   </thead>

                   <tfoot>
                       <tr>
                           <th scope="col" class="manage-column column-name" style="">Photo</th>
                           <th scope="col" class="manage-column column-name" style="">Submitted</th>
                           <th colspan="2" scope="col" class="manage-column column-name" style="text-align: center;"><span class="displaying-num">Approved Photos: <?php echo $total; ?></span></th>
                       </tr>
                   </tfoot>

                   <tbody>
                       <?php if( $entries ) { ?>

                           <?php
                           $count = 1;
                           $class = '';
                           foreach( $entries as $entry ) {
                               $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
                               $photoId = $entry->getObjectId();
                           ?>

                           <tr <?php echo $class; ?> >
                               <td><?php echo $thickbox ?><a href="<?php echo $entry->image->getURL(); ?>?TB_iframe=true&width=auto&height=auto" class="thickbox"><img src="<?php echo $entry->thumbnail->getURL() ?>"></a></td>
                               <td><?php echo $entry->getCreatedAt()->format('m/d/Y'); ?></td>
                               <td align="center" style="padding-top:0px;"><form id="parse_unapprove" name="parse_unapprove_form" method="post" method="post"><div><p><input type="hidden" name="ob_id" value="<?php echo $photoId; ?>"/></p></div><div><input type="submit" id="push_button" class="button-primary" name="parse_unapprove_push_btn" value="Unapprove" style="width:95px;"/></div></form></td>
                               <td align="center" style="padding-top:0px;"><form id="parse_delete" name="parse_delete_form" method="post" method="post"><div><p><input type="hidden" name="ob_id" value="<?php echo $photoId; ?>"/></p></div><div><input type="submit" id="push_button" class="button-primary" name="parse_delete_push_btn" value="Delete" style="background:#F26969; border-color:#D44040; width:75px;"/></div></form></td>
                           </tr>

                           <?php
                               $count++;
                           }
                           ?>

                       <?php } else { ?>
                       <tr>
                           <td colspan="2">No posts yet</td>
                       </tr>
                       <?php } ?>
                   </tbody>
               </table>

               <?php

               if ( $page_links ) {
                   echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0; float:left;">' . $page_links . '</div></div>';
               }

               echo '</div>';
                /** End Tab 2 */
        		break;
        	    case '2':

              $photos = new ParseObject("Photos"); // set Parse Object to "Photos"
              $query = new ParseQuery("Photos");
              $query->EqualTo("approved", false);  //Filters to Unapproved Photos
              $query->descending("createdAt"); // Order by most recent uploads first

              $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
              $limit = 5;
              $offset = ( $pagenum - 1 ) * $limit;
              $parseData = $query->find();
              $entries = array_slice($parseData, $offset, $limit );
              $thickbox = add_thickbox();

              $total = count($parseData);
              $num_of_pages = ceil( $total / $limit );
              $page_links = paginate_links( array(
                  'base' => add_query_arg( 'pagenum', '%#%' ),
                  'format' => '',
                  'prev_text' => __( '&laquo;', 'aag' ),
                  'next_text' => __( '&raquo;', 'aag' ),
                  'total' => $num_of_pages,
                  'current' => $pagenum
              ) );

              echo '<div class="wrap">';

              ?>

              <table class="widefat" style="max-width: 375px;">
                  <thead>
                      <tr>
                          <th scope="col" class="manage-column column-name" style="">Photo</th>
                          <th scope="col" class="manage-column column-name" style="">Submitted</th>
                          <th colspan="2" scope="col" class="manage-column column-name" style="text-align: center;"><span class="displaying-num">Unapproved Photos: <?php echo $total; ?></span></th>

                      </tr>
                  </thead>

                  <tfoot>
                      <tr>
                          <th scope="col" class="manage-column column-name" style="">Photo</th>
                          <th scope="col" class="manage-column column-name" style="">Submitted</th>
                          <th scope="col" class="manage-column column-name" style=""></th>
                          <th scope="col" class="manage-column column-name" style=""></th>
                      </tr>
                  </tfoot>

                  <tbody>
                      <?php if( $entries ) { ?>

                          <?php
                          $count = 1;
                          $class = '';
                          foreach( $entries as $entry ) {
                              $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
                              $photoId = $entry->getObjectId();
                          ?>

                          <tr <?php echo $class; ?> >
                              <td><?php echo $thickbox ?><a href="<?php echo $entry->image->getURL(); ?>?TB_iframe=true&width=auto&height=auto" class="thickbox"><img src="<?php echo $entry->thumbnail->getURL() ?>"></a></td>
                              <td><?php echo $entry->getCreatedAt()->format('m/d/Y'); ?></td>
                              <td align="center" style="padding-top:0px;"><form id="parse_approve" name="parse_approve_form" method="post" method="post"><div><p><input type="hidden" name="ob_id" value="<?php echo $photoId; ?>"/></p></div><div><input type="submit" id="push_button" class="button-primary" name="parse_approve_push_btn" value="Approve" style="width:75px;"/></div></form></td>
                              <td align="center" style="padding-top:0px;"><form id="parse_delete" name="parse_delete_form" method="post" method="post"><div><p><input type="hidden" name="ob_id" value="<?php echo $photoId; ?>"/></p></div><div><input type="submit" id="push_button" class="button-primary" name="parse_delete_push_btn" value="Delete" style="background:#F26969; border-color:#D44040; width:75px;"/></div></form></td>
                          </tr>

                          <?php
                              $count++;
                          }
                          ?>

                      <?php } else { ?>
                      <tr>
                          <td colspan="2">No posts yet</td>
                      </tr>
                      <?php } ?>
                  </tbody>
              </table>

              <?php

              if ( $page_links ) {
                  echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0; float:left;">' . $page_links . '</div></div>';
              } //END if $page_links

              echo '</div>';

        		break;
          } //END switch $tab
      } //END function content_tabs
  } //END Options_Manager
?>
