<?php
// FRC5881 Unofficial TBA Slack Bot
// Copyright (c) 2016.
//
// This program is free software: you can redistribute it and/or modify it under the terms of the GNU
// Affero General Public License as published by the Free Software Foundation, either version 3 of
// the License, or any later version.
//
// This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License along with this
// program.  If not, see <http://www.gnu.org/licenses/>.


namespace TBASlackbot\slack\commands;

use Slack\Message\Attachment;
use TBASlackbot\slack\ProcessMessage;

/**
 * Handles responses to the various help-related commands.
 * @package TBASlackbot\slack\commands
 * @author Brian Rozmierski
 */
class Help
{
    /**
     * Send to the user a help message, optionally detailed by the command/group the user wishes help with.
     *
     * @param string $teamId Slack TeamId
     * @param array $channelCache Channel Cache as stored by the DB
     * @param string $helpCommand Command/group user wants more specific help with, may be null or invalid
     * @param string|null $replyTo User to @ mention in the reply, or null to not mention user
     */
    public static function helpRouter($teamId, $channelCache, $helpCommand, $replyTo = null) {
        if ($helpCommand) {
            switch($helpCommand) {
                case 'channel':
                case 'channels':
                    $helpText = "When I first join your team, team members can contact me via direct message and "
                        . "issue commands and subscribe to team updates privately. However, one of my more powerful "
                        . "features allows a team admin, or permitted user to *_/invite_* me to another channel.\n\n"
                        . "Once I'm invited to the channel users can interact with me, in conversation, by linking "
                        . "my name. I'll reply to the user that asked me, but I'll send it to the whole channel so "
                        . "everyone can see.\n\n"
                        . "You can even ask me _@tbabot_ status 5881 in the middle of a sentence and I'll answer.\n\n"
                        . "If you want me to leave a channel, just send a *_/remove_* command to the channel.\n\n"
                        . "*Warning* for this to work Slack sends me every message in every channel I'm invited to. "
                        . "Right now my programmers are working hard on testing and making sure I'm fit as a fiddle, "
                        . "so channel messages are logged. In the future I will log only the channel, and the portion "
                        . "of the message I thought was a command. I don't understand anything but my commands, and "
                        . "my programmers don't want to read your private chats either.";
                ProcessMessage::sendReply($teamId, $channelCache, $helpText, $replyTo);
                    break;
                case 'subscribe':
                case 'follow':
                case 'unfollow':
                    $helpText = "You can subscribe, or follow, a team through their events live, and as I get updates "
                        . "from The Blue Alliance, I'll parse them and send them long to the channel I was asked to "
                        . "follow them from. Here are the commands regarding following a team:";

                    $attachment =  new Attachment('Following a Team for Live Updates', '*_follow [team number]_* '
                        . '_[*all* | result | summary]_ - I\'ll listen for competition updates for the team, '
                        . 'and by default tell you everything I hear, including match schedule updates and alerts for '
                        . 'upcoming matches, match result, and standing updates. If you want me to be less chatty, '
                        . 'add the word _result_ and I\'ll only send updates after matches, or for even less, add '
                        . '_summary_ to your request and I\'ll only send you summary updates at the end of '
                        . 'competition.');
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    $attachment =  new Attachment('Un-Following a Team', '*_unfollow [team number]_* - I\'ll stop '
                        . 'sending competition updates for this team. If you need to change how chatty '
                        . 'I am, instead of _unfollowing_ just send me another _follow_ request and I\'ll update '
                        . 'the existing subscription.');
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    $attachment =  new Attachment('Listing Followed Teams',
                        '*_following_* - I\'ll tell you which teams I\'m following for you in the channel.');
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                ProcessMessage::sendReply($teamId, $channelCache, $helpText, $replyTo, $attachments);
                    break;
                case 'changelog':
                    $helpText = "I'm always getting upgrades, changes, and improvements. Most of the time my users "
                        . "never notice, but sometimes my commands change, or I get new features users might want "
                        . "to know about. You'll find the recent changes below:";

                    $attachment =  new Attachment('Washington Peak Performance - Sept 9, 2016',
                        "We've confirmed the FMS at Peak Performance is not hooked up to FIRST HQ and thus there "
                        . "will be no live updates coming from the field, thus no updates from @tbabot.");
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    $attachment =  new Attachment('Making Feedback Easier - Sept 9, 2016',
                        "We've add a new command to TBABot, *_feedback_* that can be used to send us comments, or "
                        . "suggestions on TBABot, or even just a quick \"Thanks!\" Please feel free to use it "
                        . "often, especially if you have a problem. Please note that _everything_ after the word "
                        . "feedback is saved and logged, so if you're sending feedback from your super-secret "
                        . "strategy channel, make sure it's only your feedback in the message to @tbabot.");
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    $attachment =  new Attachment('Following Teams This Weekend - Expectations - Sept 9, 2016',
                        "This weekend is our first live event for TBABot , "
                        . "<https://www.thebluealliance.com/event/2016wapp|Peak Performance> and, to be honest, "
                        . "we're not entirely sure what to expect from an off-season event. The bot is ready to "
                        . "handle and precess updates from TBA for the 20-odd teams competing. What we do know is "
                        . "the information output from the bot as a result of TBA updates is not final, but should "
                        . "still be quite useful. We welcome feedback to improve them. We also know that some "
                        . "messages will arrive faster than the TBA API cache is invalidated. You may have seen "
                        . "this year notifications on your Android TBA app about schedule updates, but when you "
                        . "went to look, there was no schedule posted. We fear this may affect us as well, which "
                        . "is why there is a link to the TBA website (which doesn't suffer this problem) on these "
                        . "messages. Lastly, because these are off-season events, we're not sure what, if any "
                        . "messages TBA will actually send us, as they all result from the local FMS. For example, "
                        . "if you have a _summary_ subscription for a team, you get updates at the end of "
                        . "competition. This is triggered by a message that awards are updated, and a check to see "
                        . "if any matches are left to be played. If an event doesn't give awards, or doesn't post "
                        . "them in the FMS, we won't receive that notification, and won't send the summary. So, "
                        . "we ask all of you to bear with us as we learn exactly what works, and doesn't work, "
                        . "during these off-season events. The good news, however, these should all be non-issues "
                        . "for official events, as long as the FMS is connected to the internet, and say, the power "
                        . "doesn't go out. :smile:");
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    $attachment =  new Attachment('Group Messaging Fixed - Sept 8, 2016',
                        'Due to an oversight in selecting the messages to forward in the Slack Event API, the bot '
                        . 'was unable to see, or respond to, messages sent in private groups. Thanks to team 5012 '
                        . 'for pointing out the problem and helping troubleshoot.');
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    $attachment =  new Attachment('Personality Matrix / info and detail Changes - Sept 7, 2016',
                        'On the good news front, tbabot now has a bit more personality when giving error messages. '
                        . 'There\'s even some rare error messages that have a _very_ low chance of appearing. On '
                        . 'the bad news front, the easter eggs have been temporarily disabled, so you can stop '
                        . 'flooding the bot with requests for the chute door. (No that wasn\'t one of them.)' . "\n"
                        . 'Thanks for the feedback, as a result changes have been made to the *_info_* and '
                        . '*_detail_* commands to make the output a bit cleaner. A link to the team TBA page '
                        . 'has also been added to the detail output.');
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    $attachment =  new Attachment('Event Eliminations and Detail - Sept 6, 2016',
                        '*_detail_* now lists Eighth-finalists, quarterfinalists, and semifinalists in the awards '
                        . 'section of the event. We welcome feedback on it\'s placement, and we know full well it\'s '
                        . 'not an official award, however, from an ease of use standpoint it seemed best.');
                    $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
                    $attachments[] = $attachment;

                    ProcessMessage::sendReply($teamId, $channelCache, $helpText, $replyTo, $attachments);
                    break;
                default:
                    self::sendHelpAttachment($teamId, $channelCache, $replyTo);
            }
        } else {
            self::sendHelpAttachment($teamId, $channelCache, $replyTo);
        }
    }

    /**
     * Send the user the basic help message.
     *
     * @param string $teamId Slack TeamId
     * @param array $channelCache Channel Cache as stored by the DB
     * @param string|null $replyTo User to @ mention in the reply, or null to not mention user
     */
    private static function sendHelpAttachment($teamId, $channelCache, $replyTo = null) {
        /**
         * @var Attachment[]
         */
        $attachments = array();

        $attachment = new Attachment('Basic Team Information',
            '*_info [team number]_* - I\'ll lookup name and location information for the team number you ask for.');
        $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
        $attachments[] = $attachment;

        $attachment =  new Attachment('Detailed Team Information', '*_detail [team number]_* - I\'ll give you a '
            . 'detailed view of the team, including location, website, rookie year, a summary of their official '
            . 'match record for the current season, and a summary of this season\'s competitions.');
        $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
        $attachments[] = $attachment;

        $attachment =  new Attachment('Team Competition Status', '*_status [team number]_* - I\'ll give you the '
            . 'current competition status and ranking for the team number you ask for. If the team isn\'t '
            . 'competing I\'ll give you information on their last competition result as well as when their '
            . 'next competition is.');
        $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
        $attachments[] = $attachment;

        $attachment =  new Attachment('Following a Team for Live Updates', 'More information about following, or '
            . 'subscribing, to a team\'s updates is available by asking me *_help subscribe_*.');
        $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
        $attachments[] = $attachment;

        $attachment =  new Attachment('What\'s new or changed?', 'I\'m always getting updates and improvements. '
            . 'If you\'d like to know more about them, ask about *_help changelog_*.');
        $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
        $attachments[] = $attachment;

        $attachment =  new Attachment('Sending Feedback', 'My creators welcome and comments, good or bad, and '
            . 'suggestions for improvement. Just type *_feedback_* and then your message, and I\'ll send it to '
            . 'them right away.');
        $attachment->data['mrkdwn_in'] = ['text', 'pretext', 'fields'];
        $attachments[] = $attachment;

        ProcessMessage::sendReply($teamId, $channelCache, "Oh the things I can do! In a direct message, you can just send me "
            . "one of the commands. If you're in a channel with multiple people I'll listen for my name "
            . "to be mentioned first.\n", $replyTo, $attachments);
    }
}