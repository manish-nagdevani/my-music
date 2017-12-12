<?php
session_start();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'DbConnection.php';
require_once 'SqlQueries.php';

$db_conn = new DBConnection();
$conn = $db_conn->getDBConnection();


//$artist_title = htmlspecialchars($_GET['aname']);
if (isset($_GET['uname']))
$username1 = htmlspecialchars($_GET['uname']);
$username=$_SESSION['username'];
//$username=$_SESSION['username'];
$user_info = fetch_user_profile_details($conn, $username);

function fetch_user_profile_details($conn, $username) {
    //$user_info = array();
    $user_info['username']=$username;
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = fetch_user_bio_details();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['UName'] = $rows['UName'];
    $user_info['Email'] = $rows['Email'];


    $sql = fetch_user_followers_count();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['followers_count'] = $rows['followers_count'];


    $sql = fetch_user_followers();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['followers'] = $rows;


    $sql = fetch_user_following_count();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_info['following_count'] = $rows['following_count'];


    $sql = fetch_user_following();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['following'] = $rows;

     $sql = fetch_fav_artists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['fav_artists'] = $rows;

     $sql = fetch_self_playlists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['self_playlist'] = $rows;

    $sql = fetch_other_users_playlists();
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_info['users_playlist'] = $rows;

            $sql = does_user_follow_user1();
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $username1]);
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_info['does_follow'] = $rows['rec_count'] > 0 ? TRUE : FALSE;


        $sql = fetch_user_followers_count();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username1]);
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_info['follower_count'] = $rows['follower_count'];
    
    return $user_info;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
<?php require_once 'header.html'; ?>
        <div id="user-pg-container">
            
            <?php if (isset($user_info['error'])): ?>
                <div class="alert alert-danger alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $user_info['error']['message']; ?>
                </div>
            <?php endif; ?>

            <?php if (!isset($user_info['error'])): ?>
                
                <!-- Displaying Artist Info -->
                <div id="artist-bio" class="row">
                    <div id="artist-image" class="col-sm-5">
                        <img title="<?php echo ucwords($user_info['username']); ?> image" alt="<?php ucwords($user_info['username']) ?>" src="artist-images/download.png">
                    </div>
                    
                    <div id="summary-and-bio" class="col-sm-7">
                   
                        <div id="artist-summary">
                            <h1><?php echo ucwords($user_info['username']); ?> </h1>
                            <p> <?php echo $user_info['Email'] ?> 
                            

                            </p>
                            <p> <a href="following.php"><?php echo $user_info['following_count'] ?> Following</a> | <a href="followers.php"><?php echo $user_info['followers_count'] ?> Followers</a></p>
                            <p> <a href="index.html"> Create Playlist </a> </p>
                        </div>
                    
                        
                        <div id="" class="row">
                            <div class="col-sm-3">
                                <form action="FollowUnfollow.php" method="post" class="artist-like-form">
                                    <input type="hidden" value="<?php echo $username1?>" id="username1" name="username1"/>
                                    <?php if($user_info['does_follow'] == 1):?>
                                        <input type="checkbox" class="" id="follow-check" name="follow-check" checked> Follow
                                    <?php else: ?>
                                        <input type="checkbox" class="" id="follow-check" name="follow-check"> Follow
                                    <?php endif; ?>
                                        <input type="hidden" name="destination" value="<?php echo $_SERVER["REQUEST_URI"]; ?>"/>
                                        <button type="submit"  class="form-sbmt-btn btn btn-default">Confirm</button>
                                </form>
                            </div>
                        </div>
                        <?php if(isset($_GET['success'])):?>
                                <div class="col-sm-4" id="success-msg">
                                    <?php if($user_info['does_follow'] == 1):?>
                                            <p class="alert alert-success">You have Followed <?php echo $username1;?></p>
                                        <?php else:?>
                                            <p class="alert alert-info">You have Unfollowed <?php echo $username1;?></p>
                                    <?php endif;?>     
                                </div>
                        <?php endif;?> 
                    </div>
                </div>   


                
        
                
                <?php if($user_info['fav_artists']):?>
                <div id = "top-songs">
                    <h3>Favourite Artists:</h3>
                    <ul id="top-songs-headers" class="row">
                        <li class="song-header-cnt col-sm-2">#</li>
                        <li class="song-header-title col-sm-10">ARTIST NAME</li>
                       
                        
                    </ul>
                    <?php foreach ($user_info['fav_artists'] as $i => $arr): ?>
                        <ul id ="pay-load">
                            <li class="song-header-cnt col-sm-2"><?php echo $i + 1; ?></li>
                             <?php $temp1= ucwords($arr['fav_artists']); ?>
                            <li class="song-header-title col-sm-10"><a href="artistbio.php?aname=<?php echo $temp1; ?>"> <?php echo ucwords($arr['fav_artists']); ?></a></li>
                              
                        </ul>

                    <?php endforeach; ?>
                </div>
                <?php endif;?>
            </br></br></br>

                <?php if($user_info['self_playlist']):?>
                <div id = "top-songs">
                    <h3>Your Playlists:</h3>
                    <ul id="top-songs-headers" class="row">
                        <li class="song-header-cnt col-sm-2">#</li>
                        <li class="song-header-title col-sm-10">PLAYLIST NAME</li>
                       
                        
                    </ul>
                    <?php foreach ($user_info['self_playlist'] as $i => $arr): ?>
                        <ul id ="pay-load">
                            <li class="song-header-cnt col-sm-2"><?php echo $i + 1; ?></li>
                            <li class="song-header-title col-sm-10"><a href="playlist.php"> <?php echo ucwords($arr['self_playlist']); ?></a></li>
                              
                        </ul>

                    <?php endforeach; ?>
                </div>
                <?php endif;?>
                <!-- Displaying Top songs -->
               
                
            <?php endif; ?> 
        </div>
    </body>
</html>

