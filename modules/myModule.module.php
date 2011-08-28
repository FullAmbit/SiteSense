<?php 
 
function page_buildContent($data,$db) { 
} 
 
function page_content($data) { 
     theme_contentBoxHeader('Test Module'); 
     echo ' 
          <p> 
               This is just a test module 
          </p>'; 
     theme_contentBoxFooter(); 
} 
 
?>