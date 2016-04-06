<?php
/********************** Register Bison Media—Video Feed  *********************
*******************************************************************************
* CURRENTLY UNUSED PAGE, COMMENTED OUT.
*/

  // Call bison_media_menu function to load plugin menu in dashboard
  //add_action( 'admin_menu', 'bison_media_menu' );

  // Create WordPress admin menu
  function bison_media_menu(){

    $page_title = 'Bison Media—Video Feed';
    $menu_title = 'Bison Media—Video Feed';
    $capability = 'manage_options';
    $menu_slug  = 'bison-media-info';
    $function   = 'bison_media_page';
    $icon_url   = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMiIgYmFzZVByb2ZpbGU9InRpbnkiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBhdGggZmlsbD0iIzk5OTk5OSIgZD0iTTI1MC4yMDUsNy4wMjFjMC41MTktMC4xMTIsMC41NzUsMC4yMzcsMC42MSwwLjYwOWMtMS43OTIsNS41MzEtNS45OCwxMy45NDQtNy4zMiwyMS45NjFjMjcuNDM1LTExLjkwMSw1NC42MTQsMC4yODcsNzMuMjAxLDE1Ljg2YzE0LjY5NywxMi4zMTQsMzAuMzA1LDMzLjI2NSw1NC4yOTEsMjMuMThjMS4yMzktMTEuNjM1LTMuMDE4LTIwLjk0My00LjI3MS0yOC4wNjFjMjMuNTIxLDUuNzkxLDQ0LjgwMiwxOS42NDYsNjEuMDAyLDM3LjgyMWMxOC4yMTMsMjAuNDM2LDMyLjU0Miw0NC4yNTUsMzAuNSw4MS4xMzJjLTAuODk5LDE2LjI2MS00LjkzNiwyOS43NzYtMTIuMiw0MC44N2M4Ljc3NSw5LjkzMiwyMS4yMDUsMTYuMjEsMzQuNzcxLDIxLjM1MWMtMTAuOTIyLDE3LjU0NS0yNC4wODQsMzIuODUxLTQ4LjE5LDM3LjIxMWM0Ljg2NiwyNS4wMjctNS40MzYsNDQuMjctMTkuNTIxLDU0LjI5MWMyLjcsMTcuNjI2LDE0LjcyOCw0NS41MjUsMS4yMiw1OS4xNzFjNy4yNjIsMjAuNjQyLTExLjM2LDMwLjg0NC0xOC4zLDQ2Ljk3MWMtNy4xMzMsMTYuNTgtOS4xMSwzMy4wMzItMjQuNCw0MC44NzFjLTMuMTg4LDEuNjM0LTcuMDIyLDEuNjU1LTkuNzYxLDMuMDVjLTIuOTAzLDEuNDgtNS4zMjIsNC41NzItNy45Myw2LjcxYy05Ljc5OCw4LjAzMy0yMy4zMTMsMTMuNzkzLTQzLjkyMSwxMi4yYy0xNy44MjYsMTQuMzA3LTQ0LjkwNCwyNC4zNzQtNzcuNDcyLDIyLjU3MWMtMjMuNTY4LTEuMzA2LTQ1Ljk5Mi05LjA3My02Mi4yMjEtMTcuMDgxYzguNDY3LDAuMjM0LDIxLjg3NywxLjUzMiwyOS44OTEtMS44M2MtNDAuOTA2LTcuNjkxLTY3LjMzMy0yOS44NjItNzguNjkyLTY3LjEwMWM2Ljg3LDQuMzEzLDEzLjQ4MSw4Ljg4NiwyMy43OTEsOS43NmMtMTEuMDMzLTE1LjMwMi0xNC40NTMtMzcuNzc4LTI2Ljg0MS01My4wNzFjLTkuNjcyLTExLjkzOS0yMi43MjMtMTguNzQ0LTM5LjA0MS0yMy43ODljNS40NTYtOC43NzksMTMuNTg3LTE0Ljg4MiwyMy4xODEtMTkuNTIxYy0xNS41MjgtMTYuMTUtNDAuMzA2LTI1Ljc2LTcxLjM3MS0yNC40YzUuMzItMzAuODc0LDEzLjA2NS01OS4zMjMsMjMuMTgxLTg1LjQwMWMtNy4xNjIsMS4zODEtMTUuMTg0LDcuMTgyLTIzLjE4MSw5Ljc2YzI0Ljc0NS02OC4zNTgsNjIuMTUyLTEyNi43ODEsMTE3LjEyMi0xNjUuMzEzYzcuMjAxLTUuMDQ4LDEzLjE4LTEwLjEyOSwxOS41MjEtMTYuNDdDMTg4LjIxNSwyOS45NzEsMjE1LjI0NywxMy41NjYsMjUwLjIwNSw3LjAyMXogTTE0MS42MjMsMTQwLjYxM2MtMS41NjYsMjguMjc2LDcuNTUxLDQ3LjQ1MSwyMS45Niw1OS4xNzJjMy44MDUsMy4wOTQsOC40NzksNi4yMzYsMTMuNDIxLDcuMzE5Yy01LjYxNi0xMi4zNzEtMTAuNTA2LTM0LjY1OC00Ljg4LTUwLjYzMWMwLjk3LDkuNTIyLDQuODc5LDE5LjEzNywxMC4zNywyNi44NDFjNS4xMzUsNy4yMDUsMTMuMjQ3LDE0Ljc1NiwyMS45NiwxNS44NmMxMy40MjYsMS43MDEsMTcuMTg0LTExLjUxNiwxNy42OS0yMi41N2MwLjYzNi0xMy44ODItNC4wNDEtMjUuNTAxLTcuOTMtMzQuMTYxYy0xOC4wNDQtMC43NDktMjcuNjMzLTEyLjM5OS0yOC42NzEtMjguMDYxYy0yLjIxLTMzLjM0NiwyMC43MjktNjEuODA2LDMzLjU1MS03OC4wODFDMTgzLjc0NCw1Ni4wNTEsMTQ0LjQ4Niw4OC45MDYsMTQxLjYyMywxNDAuNjEzeiBNNDMzLjIwOSwxODQuNTM0YzQuMDc0LTguODk0LDcuMTM5LTE3LjYzOCw3LjkzLTI4LjY3YzIuODM2LTM5LjU4My0xOS4zOTEtNjMuODc3LTQwLjI2MS03OS45MTJjLTIuNzM5LTIuMTA0LTUuMTY5LTUuMDcxLTguNTQtNS40OWMxMS4zOCwxNy44OTIsMjYuMiw1MC44MTMsMTQuNjQsNzguMDgyYzguNjY3LTEuNzA0LDEzLjY0OC03LjA5MiwxOC45MTEtMTIuMkM0MzEuNzcxLDE0OC41MTksNDM1LjYwNiwxNjgsNDMzLjIwOSwxODQuNTM0eiIvPjwvZz48L3N2Zz4=';
    $position   = 5; // Use decimal places to avoid position conflicts?
    $tabs       = ['Unapproved Photos', 'Approved Photos', 'Debug'];

    add_menu_page( $page_title,
                   $menu_title,
                   $capability,
                   $menu_slug,
                   $function,
                   $icon_url,
                   $position,
                   $tabs, 'options' );

    // Call update_bison_media function to update database
    add_action( 'admin_init', 'update_bison_media' );

  }

  // Create function to register plugin settings in the database
  function update_bison_media() {
    register_setting( 'bison-media-info-settings', 'bison_media' );
  }

  /**
   * Create Admin Page: "Bison Media—Video Feed" (Currently Unused)
   */
  function bison_media_page() {
    echo '<h1>In Development</h1>';
    //Page Code Goes HERE
  }
