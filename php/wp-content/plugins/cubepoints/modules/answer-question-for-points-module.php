<?php
 
/** Answer Question for Points Module */
 
cp_module_register(__('Answer Question for Points', 'cp') , 'question4points' , '1.0', 'Tosh Hatch', 'http://www.SlySpyder.com', 'http://cubepoints.com/forums/topic/answer-question-for-points-module/' , __('This module allows your members to answer questions and earn points for it!', 'cp'), 1);
 
if(cp_module_activated('question4points')){
 
 
 
/* ****************** Shortcode for Questions for Points ************ */
 
function questions4points($atts) {
 
        extract(shortcode_atts(array(
            'answer' => '',
            'points' => '',
            'limit' => '',
        ), $atts));
       
        if (isset($_POST['guessedanswer'])) {
               
                get_currentuserinfo();
                if ( !is_user_logged_in() ) {
                        $output .= 'You must be logged in to get points!';
                        return $output;
                }
                               
                global $wpdb, $post;
                define('CUBEPTS3', $wpdb->prefix . 'cp');
                $guessed = strtolower($_POST["guessedanswer"]);
                $answer = strtolower($answer);
                $cp_question_postID = get_the_ID();
                $current_user = wp_get_current_user();
                $first_correct_answers = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS3." WHERE type = 'cp_question' AND data = ".$cp_question_postID);
                $correct_answers = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS3." WHERE uid = '".$current_user->ID."' AND type = 'cp_question' AND data = ".$cp_question_postID);   
               
                if ($correct_answers >= 1) { // Check if they already got points for it. Enter 1 for allowing just 1 answer per post/page.
                       
                        $output .= '<p><strong>You already got points, nice try!</strong></p><br />';
                       
                } else {
               
                        if ($guessed == $answer) {
                               
                                if ($first_correct_answers <= 10) { // First 10 to answer the question correct get double points!
                                        $points = $points * 2;
                                }
                               
                                        if ($first_correct_answers < $limit) { // In the short code limit="10" mean only 10 can get points
                                                $output .= '<p><strong>Enjoy your '.$points.' points!</strong></p><br />';
                                                cp_points('cp_question', $current_user->ID, $points, $cp_question_postID);
                                        } else {
                                                $output .= '<p><strong>Sorry, a little too late!</strong></p>';
                                        }
 
                        } else {
                                $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
                                $output .= '<p><strong style="color:red">Incorrect, you guessed: '.$guessed.'<br /><br /><a href="'.$url.'">Go back</a> and try again.</strong></p><br />';
                        }
                } // End if they already got points.
               
        return $output;
        }
       
                $output .= '<form name="question" method="post" action="">
                                  <p><label><input type="text" name="guessedanswer" id="guessedanswer" /></label></p>
                                  <p><label><input type="submit" name="button" id="button" value="Submit"></label></p>
                            </form>';
                           
        return $output;
       
}
add_shortcode('question', 'questions4points');
add_action('cp_logs_description','questioncorrectlogit', 10, 4); // Log it
function questioncorrectlogit($type,$uid,$points,$data){
        if($type != 'cp_question') { return; }
        echo 'Answered correctly';
}
/* ****************** Shortcode for Questions for Points ************ */
 
       
 
}
?>