<?php

use Facades\App\Feed;
use App\Http\Controllers\BotManController;
use App\Notifications\Subscribed;
use App\User;

$botman = resolve('botman');

$botman->hears('.*(Hi|Hello|Hey).*', function ($bot) {
    $bot->reply('Hello! What can I do for you today? Try "info" for more information.');
});

$botman->hears('.*(info|help).*', function ($bot) {
    $bot->reply('Right now, your best option is to send the message "subscribe", which will sign you up to be notified when we have new episodes; or "unsubscribe" if you want to stop receiving notifications. You can also send "episodes" for a list of all episodes.');
});

$botman->hears('unsubscribe.*', function ($bot) {
    $user = $bot->getUser();

    Log::info('FB User unsubscribed! ' . $user->getId());
    User::where(['facebook_id' => $user->getId()])->delete();

    $bot->reply("You're now unsubscribed from " . config('app.name') . "!");
});

$botman->hears('subscribe.*', function ($bot) {
    $user = $bot->getUser();

    Log::info('FB User subscribed! ' . $user->getId());
    User::firstOrCreate(['facebook_id' => $user->getId()]);

    $bot->reply("You're now subscribed to " . config('app.name') . "!");
});

$botman->hears('episodes', function ($bot) {
    $episodes = Feed::getItems();

    $return = '';

    foreach ($episodes as $episode) {
        $return .= $episode->get_title() . ": " . $episode->get_permalink() . "\n";
    }

    $bot->reply($return);
});

$botman->fallback(function ($bot) {
    $bot->reply("Sorry, I don't quite know what you mean. Could you try again?");
});
