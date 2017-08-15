<?php
include_once 'vendor/autoload.php';

//Login with an existing account
$sarahah = new Sarahah("youremail@email.com", "mypassword");

//Create a new account
$sarahah = Sarahah::register("TustinROX", "youremail@email.com", "mypassword", "Tustin");

//Get profile info
var_dump($sarahah->getProfile());

//Send message (get the userId-guid from getReceivedMessages() or something)
$sarahah->createMessage('userId-guid-here', 'API message!');

//Show all sent messages
var_dump($sarahah->getSentMessages());

//Show all favorited messages
var_dump($sarahah->getFavoritedMessages());

//Show all received messages
var_dump($sarahah->getReceivedMessages());

//Search for a user
var_dump(Sarahah::searchUsers("Tustin"));

//Favorite a message
$sarahah->favoriteMessage('messageId-here', "true");

//Unfavorite a message
$sarahah->favoriteMessage('messageId-here', "false");

//Block a user
$sarahah->blockUser('messageId-from-user');

//Report a message
$sarahah->reportMessage('messageId-from-user');

//Update profile
$sarahah->updateProfile("Tustin rox!", "youremail@email.com", false, true, true, true, true);