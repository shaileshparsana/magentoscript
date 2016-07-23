<!-- BEGIN: Twitter website widget (http://twitterforweb.com) -->
<div style="width:236px;font-size:8px;text-align:right;"><script type="text/javascript">
document.write(unescape("%3Cscript src='http://twitterforweb.com/twitterbox.js?username=suddorasports&settings=0,1,3,236,355,f4f4f4,0,c4c4c4,101010,1,1,336699' type='text/javascript'%3E%3C/script%3E"));</script>Created by: <a href="http://twitterforweb.com" target="_blank">twitter website widget</a></div>
<!-- END: Twitter website widget (http://twitterforweb.com) -->
<!-- BEGIN: Twitter website widget (http://twitterforweb.com) -->
<div style="width:236px;font-size:8px;text-align:right;"><script type="text/javascript">
document.write(unescape("%3Cscript src='http://twitterforweb.com/twitterbox.js?username=customonit&settings=0,1,3,236,355,f4f4f4,0,c4c4c4,101010,1,1,336699' type='text/javascript'%3E%3C/script%3E"));</script></div>
<!-- END: Twitter website widget (http://twitterforweb.com) -->

<?php  

  
require_once 'twitteroauth.php';
$oTwitter = new TwitterOAuth 
    ('u8E2Ux6pLlry32fURIgTfw',
     '3HJUJjJosnJbP4xcSUBUyPXAPiWkHhTH787Ri3C6AE',
     '106142106-u2lrwniWUzaUkNyrn2AXBd8oWoGqLLTXbTQJz3vb',
     'jqubFiBfx4DQHhZFN0XFamxJMmaCm9AvLKRg3aKbU');

//FULL FOLLOWERS ARRAY WITH CURSOR ( FOLLOWERS > 5000)
    $e = 0;
    $cursor = -1;
    $full_followers = array();
    do {
        //SET UP THE URL
      $follows = $oTwitter->get("followers/ids.json?screen_name=customonit&cursor=".$cursor);
      $foll_array = (array)$follows;

      foreach ($foll_array['ids'] as $key => $val) {

            $full_followers[$e] = $val;
            $e++; 
      }
           $cursor = $follows->next_cursor;

      } while ($cursor > 0);
echo "Number of followers:" .$e. "<br /><br />";









////FULL FRIEND ARRAY WITH CURSOR (FOLLOWING > 5000)
//    $e = 0;
//    $cursor = -1;
//    $full_friends = array();
//    do {
//
//      $follow = $oTwitter->get("friends/ids.json?screen_name=bharatplumtree&cursor=".$cursor);
//      $foll_array = (array)$follow;
//
//      foreach ($foll_array['ids'] as $key => $val) {
//
//            $full_friends[$e] = $val;
//            $e++;
//      }
//          $cursor = $follow->next_cursor;
//
//    } while ($cursor > 0);
//    echo "Number of following:" .$e. "<br /><br />";
//
////IF I AM FOLLOWING USER AND HE IS NOT FOLLOWING ME BACK, I UNFOLLOW HIM
//$index=1;
//$unfollow_total=0;
//foreach( $full_friends as $iFollow )
//{
//$isFollowing = in_array( $iFollow, $full_followers );
//
//echo $index .":"."$iFollow: ".( $isFollowing ? 'OK' : '!!!' )."<br/>";
//$index++;
// if( !$isFollowing )
//    {
//    $parameters = array( 'user_id' => $iFollow );
//    $status = $oTwitter->post('friendships/destroy', $parameters);
//    $unfollow_total++;
//    } if ( $unfollow_total === 10) break;
//}
//echo "<br /><br />";
//
////IF USER IS FOLLOWING ME AND I AM NOT, I FOLLOW
//$index=1;
//$follow_total=0;
//foreach( $full_followers as $heFollows )
//{
//$amFollowing = in_array( $heFollows, $full_friends );
//
//echo $index .":"."$heFollows: ".( $amFollowing ? 'OK' : '!!!' )."<br/>";
//$index++;
// if( !$amFollowing )
//    {
//    $parameters = array( 'user_id' => $amFollowing );
//    $status = $oTwitter->post('friendships/create', $parameters);
//    $follow_total++;
//    } if ($follow_total === 10) break;
//}
//echo "Unfollowed:".$unfollow_total;