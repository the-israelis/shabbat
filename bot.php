<?php declare(strict_types=1);

use danog\MadelineProto\Broadcast\Filter;
use danog\MadelineProto\API;
use danog\MadelineProto\Broadcast\Progress;
use danog\MadelineProto\Broadcast\Status;
use danog\MadelineProto\EventHandler\Attributes\Cron;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\Filter\FilterCommand;
use danog\MadelineProto\EventHandler\Filter\FilterCommandCaseInsensitive;
use danog\MadelineProto\EventHandler\Message;
use danog\MadelineProto\EventHandler\Message\ChannelMessage;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;
use danog\MadelineProto\EventHandler\Message\GroupMessage;
use danog\MadelineProto\EventHandler\SimpleFilter\FromAdmin;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\LocalFile;
use danog\MadelineProto\Logger;
use danog\MadelineProto\ParseMode;
use danog\MadelineProto\Settings;
use danog\MadelineProto\SimpleEventHandler;
use danog\MadelineProto\BotApiFileId;
use danog\MadelineProto\EventHandler\CallbackQuery;
use danog\MadelineProto\EventHandler\InlineQuery;
use danog\MadelineProto\EventHandler\Query\ButtonQuery;
use danog\MadelineProto\EventHandler\Filter\FilterButtonQueryData;
use danog\MadelineProto\EventHandler\Filter\FilterIncoming;
use danog\MadelineProto\EventHandler\Update;
use Amp\File;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;

if (class_exists(API::class)) {
} elseif (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    if (!file_exists('madeline.php')) {
        copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
    }
    require_once 'madeline.php';
}

class MyEventHandler extends SimpleEventHandler
{

    public const CLOSER = "הקבוצה שלנו שומרת שבת ותהיה סגורה עד צאת השבת 🕯
🇮🇱 שבת שלום לכולם 🇮🇱"; 

    public const OPENER = "🇮🇱 שבוע טוב לכולם! 🇮🇱
הקבוצה פתוחה לכתיבת הודעות.";	

    private array $notifiedChats = [];

    public function __sleep(): array
    {
        return ['notifiedChats'];
    }

    public function getReportPeers()
    {
$ADMIN = parse_ini_file('.env')['ADMIN'];
        return $ADMIN;
    }

 #[FilterIncoming]
    public function h2(ChannelMessage $message): void 
    {
try {
$this->channels->leaveChannel(channel: $message->chatId );
}catch (\danog\MadelineProto\Exception $e) {
} catch (\danog\MadelineProto\RPCErrorException $e) {
}
    }

#[FilterCommandCaseInsensitive('start')]
    public function startCommand(Incoming & PrivateMessage  $message): void
    {
$senderid = $message->senderId;
$messageid = $message->id;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}

$txtbot = "היי <a href='mention:$senderid'>$first_name</a>, ברוך הבא 👋
הרובוט שישמור את השבת בקבוצה שלך!";

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"זמני כניסת השבת 🕯",'callback_data'=>"זמנישבת"]				   
                    ],
                    [		
['text'=>"מידע 💬",'callback_data'=>"מידע"],['text'=>"פקודות 💡",'callback_data'=>"כלהפקודות"]				   
                    ],
                    [		
['text'=>"הוסף אותי לקבוצה ➕",'url'=>"https://t.me/ShabatRobot?startgroup"]				   
                    ],
                    [	
['text'=>"קבוצת תמיכה 👥",'url'=>"https://t.me/theisraelisgroup"],['text'=>"📣 ערוץ עדכונים 📣",'url'=>"https://t.me/theisraelisbackup"]	
                    ],
                    [	
['text'=>"מבית הישראלים 🇮🇱",'url'=>"https://t.me/the_israelis"]					
        ]
    ]
];
$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];
$this->messages->sendMessage(peer: $message->senderId, reply_to: $inputReplyToMessage, message: "$txtbot", reply_markup: $bot_API_markup, parse_mode: 'HTML');

    if (!file_exists("data")) {
mkdir("data");
}
    if (!file_exists("data/$senderid")) {
mkdir("data/$senderid");
}
    if (file_exists("data/$senderid/grs1.txt")) {
unlink("data/$senderid/grs1.txt");
}
}

#[FilterButtonQueryData('חזרה')]
public function x2command(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$txtbot = "היי <a href='mention:$userid'>$first_name</a>, ברוך הבא 👋
הרובוט שישמור את השבת בקבוצה שלך!";

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"זמני כניסת השבת 🕯",'callback_data'=>"זמנישבת"]				   
                    ],
                    [		
['text'=>"מידע 💬",'callback_data'=>"מידע"],['text'=>"פקודות 💡",'callback_data'=>"כלהפקודות"]				   
                    ],
                    [		
['text'=>"הוסף אותי לקבוצה ➕",'url'=>"https://t.me/ShabatRobot?startgroup"]				   
                    ],
                    [						  
['text'=>"קבוצת תמיכה 👥",'url'=>"https://t.me/theisraelisgroup"],['text'=>"📣 ערוץ עדכונים 📣",'url'=>"https://t.me/theisraelisbackup"]	
                    ],
                    [	
['text'=>"מבית הישראלים 🇮🇱",'url'=>"https://t.me/the_israelis"]					
        ]
    ]
];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
    if (file_exists("data/$userid/grs1.txt")) {
unlink("data/$userid/grs1.txt");
}
}
	
#[FilterButtonQueryData('זמנישבת')]
public function shabbat1command(callbackQuery $query)
{
$userid = $query->userId;  
$msgid = $query->messageId;   
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרה"]
        ]
    ]
];

$bot_API_markup2m = ['inline_keyboard' => 
    [
        [
['text'=>"⌛️",'callback_data'=>"⌛️"]
        ]
    ]
];

$editer = $query->editText($message = "<b>בודק זמנים... אנא המתן</b> ⌛️", $replyMarkup = $bot_API_markup2m, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}

$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);

if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);


$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ <u><b>זמני כניסת ויציאת השבת:</b></u>

🗓 $updatedLine3$dateshabatout

🕯 <u>זמני כניסת השבת:</u>
ירושלים: <code>$timein</code>
חיפה: <code>$timeinzman2</code>
תל אביב: <code>$timeinzman3</code>
באר שבע: <code>$timeinzman4</code>

🍷 <u>זמני יציאת השבת:</u>
ירושלים: <code>$timeout</code>
חיפה: <code>$timeoutzman2</code>
תל אביב: <code>$timeoutzman3</code>
באר שבע: <code>$timeoutzman4</code>"; 

$editer2 = $query->editText($message = "$zmanim", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

//if($editer == true){
//$this->messages->deleteMessages(revoke: true, id: [$sentMessage2]); 
//} 

}

#[FilterButtonQueryData('מידע')]
public function x3command(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$txtbot = "<b>הרובוט שישמור את השבת בקבוצה שלך!</b>

🕯 על מנת שאני יוכל לסגור את הקבוצה בשבת, יש להוסיף אותי לקבוצה שלך כמנהל עם הרשאות לחסימת משתמשים ושינוי הרשאות.

לאחר ההוספה חובה לשלוח בקבוצה את הפקודה <code>/add</code> אחרת אני לא אשמור את השבת אצלך בקבוצה...

<i>אם עדיין לא הבנתם את ההוראות או שיש לכם שאלות נוספות אתם מוזמנים לשאול בקבוצת תמיכה.</i>";

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"הוסף אותי לקבוצה ➕",'url'=>"https://t.me/ShabatRobot?startgroup"]				   
                    ],
                    [	
['text'=>"חזרה",'callback_data'=>"חזרה"]				   
        ]
    ]
];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
    if (file_exists("data/$userid/grs1.txt")) {
unlink("data/$userid/grs1.txt");
}
}

#[FilterButtonQueryData('כלהפקודות')]
public function x4command(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$txtbot = "<b>רשימת פקודות</b> 💡
/add - שליחת פקודה זו בקבוצה תוסיף את הקבוצה לבסיס נתונים על מנת שהיא תסגר בשבת!
/shabat - הצגת זמני כניסת ויציאת השבת 
/remove - הסרת הקבוצה מהבסיס נתונים... הקבוצה לא תסגר בשבת!
/settings - התאם אישית את הרובוט בקבוצה שלך. 
/stats - קבוצות שומרות שבת 

<b>מה אפשר לעשות בהגדרות</b> ⚙️
באפשרותכם להגדיר האם הקבוצה תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת!
כמו כן באפשרותכם להגדיר הודעה מותאמת אישית שתשלח בערב שבת כשהקבוצה נסגרת!

הקבוצה תיסגר לפי זמן: ירושלים 
(10 דק' לפני כניסת השבת.)
בקרוב --> בחירת זמן סגירה ⌚️ 

<b>(את הפקודות יש לשלוח בקבוצה בלבד)</b>";

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרה"]				   
        ]
    ]
];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

    #[FilterCommandCaseInsensitive('shabat')]
    public function shabatCommand(Incoming $message): void
    {
$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;


$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];
$sentMessage = $this->messages->sendMessage(peer: $chatid, reply_to: $inputReplyToMessage, message: "<b>בודק זמנים... אנא המתן</b> ⌛️", parse_mode: 'HTML');
$sentMessage2 = $this->extractMessageId($sentMessage);


$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}

$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);

if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);


$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ <u><b>זמני כניסת ויציאת השבת:</b></u>

🗓 $updatedLine3$dateshabatout

🕯 <u>זמני כניסת השבת:</u>
ירושלים: <code>$timein</code>
חיפה: <code>$timeinzman2</code>
תל אביב: <code>$timeinzman3</code>
באר שבע: <code>$timeinzman4</code>

🍷 <u>זמני יציאת השבת:</u>
ירושלים: <code>$timeout</code>
חיפה: <code>$timeoutzman2</code>
תל אביב: <code>$timeoutzman3</code>
באר שבע: <code>$timeoutzman4</code>"; 


$bot_API_markup = ['inline_keyboard' => [
                                        [
['text'=>"מבית הישראלים 🇮🇱",'url'=>"https://t.me/the_israelis"]
    ]
  ]
];
$this->messages->editMessage(peer: $message->chatId, id: $sentMessage2, message: "$zmanim", reply_markup: $bot_API_markup, parse_mode: 'HTML');



}

 public function onUpdateBotInlineQuery($update)
    {

$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}

$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);

if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);


$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ זמני כניסת ויציאת השבת:

🗓 $updatedLine3$dateshabatout

🕯 זמני כניסת השבת:
ירושלים: $timein
חיפה: $timeinzman2
תל אביב: $timeinzman3
באר שבע: $timeinzman4

🍷 זמני יציאת השבת:
ירושלים: $timeout
חיפה: $timeoutzman2
תל אביב: $timeoutzman3
באר שבע: $timeoutzman4"; 

$bot_API_markup = ['inline_keyboard' => [
                                        [
['text'=>"מבית הישראלים 🇮🇱",'url'=>"https://t.me/the_israelis"]
    ]
  ]
];


$botInlineMessageText = ['_' => 'inputBotInlineMessageText', 'message' => "$zmanim", 'reply_markup' => $bot_API_markup];
$inputBotInlineResult = ['_' => 'botInlineResult', 'id' => '0', 'type' => 'article', 'title' => 'זמני כניסת השבת', 'description' => 'לחץ כאן לשיתוף זמני השבת!', 'send_message' => $botInlineMessageText];
		  
        $this->logger("Got query ".$update['query']);
        try {
            $result = ['query_id' => $update['query_id'], 'results' => [$inputBotInlineResult], 'cache_time' => 5];


            if ($update['query'] === 'shabat') {
$this->messages->setInlineBotResults($result);
            } else {
$this->messages->setInlineBotResults($result);
            }
        } catch (Throwable $e) {
            try {
//$this->messages->sendMessage(['peer' => self::ADMIN, 'message' => $e->getCode().': '.$e->getMessage().PHP_EOL.$e->getTraceAsString()]);
$this->messages->sendMessage(['peer' => $update['user_id'], 'message' => $e->getCode().': '.$e->getMessage().PHP_EOL.$e->getTraceAsString()]);
} catch (RPCErrorException $e) {
$this->logger($e);
} catch (Exception $e) {
$this->logger($e);
}

}
}
	
   #[FilterButtonQueryData('סגור')]
public function closecommand(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$msgid = $query->messageId;
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

try {
$this->messages->deleteMessages(revoke: true, id: [$msgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
$query->answer($message = "אני לא יכול לסגור את ההודעה, סגור אותה בעצמך..", $alert = false, $url = null, $cacheTime = 0);
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
$query->answer($message = "אני לא יכול לסגור את ההודעה, סגור אותה בעצמך..", $alert = false, $url = null, $cacheTime = 0);
}
}

}

    #[FilterCommandCaseInsensitive('add')]
    public function addgroupCommand(Incoming & GroupMessage  $message): void
    {
$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;

$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "(null)";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "(null)";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "(null)";
}

$usernames = $User_Full['User']['usernames']?? null;
if($usernames != null){
$usernames = implode(" , ", $usernames);
}
if($usernames == null){
$usernames = null;
}

$me = $this->getSelf();
$me_name = $me['first_name'];
$me_id = $me['id'];

$Chat_Full = $this->getInfo($message->chatId);
$title = $Chat_Full['Chat']['title']?? null;
if($title == null){
$title = "(null)";
}

$admrgh = $Chat_Full['Chat']['admin_rights']['ban_users']?? null;

$type = $Chat_Full['type'];

if($type != "supergroup"){
$txtbot = "<b>אני פועל רק בקבוצות-על(supergroup)</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}


if($type == "supergroup"){

$channelpart = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $message->senderId ]);
if(isset($channelpart['participant']['_'])&& ($channelpart['participant']['_'] == 'channelParticipantAdmin' or $channelpart['participant']['_'] == 'channelParticipantCreator'))  $isadmin = true;
else $isadmin = false;

if($isadmin != false){
	
$channelpart2 = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $me_id ]);
if(isset($channelpart2['participant']['_'])&& ($channelpart2['participant']['_'] == 'channelParticipantAdmin' or $channelpart2['participant']['_'] == 'channelParticipantCreator'))  $isadmin2 = true;
else $isadmin2 = false;	

if($isadmin2 != false){

if($admrgh == null){
$txtbot = "<b>אין לי הרשאות ניהול מתאימות.</b>
(הרשאות לחסימת משתמשים ושינוי הרשאות)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if($admrgh != null){
	
	



if (file_exists("data/DBgroups.txt")) {
$filex = Amp\File\read("data/DBgroups.txt");  
$user1 = explode("\n",$filex);
if(!in_array($chatid,$user1)){
if($filex != null){
$filex = $filex."\n"; 
Amp\File\write("data/DBgroups.txt", "$filex"."$chatid");
$txtbot = "<b>הקבוצה נוספה לבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
    if (!file_exists("data/$chatid")) {
mkdir("data/$chatid");
}
}
if($filex == null){
$filex = null; 
Amp\File\write("data/DBgroups.txt", "$filex"."$chatid");
$txtbot = "<b>הקבוצה נוספה לבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
    if (!file_exists("data/$chatid")) {
mkdir("data/$chatid");
}
}
}
if(in_array($chatid,$user1)){
$txtbot = "<b>הקבוצה כבר בבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}	
}	

if (!file_exists("data/DBgroups.txt")) {
$filex = null; 
Amp\File\write("data/DBgroups.txt", "$filex"."$chatid");
$txtbot = "<b>הקבוצה נוספה לבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
    if (!file_exists("data/$chatid")) {
mkdir("data/$chatid");
}
}


}
}
if($isadmin2 != true){
$txtbot = "<b>אני לא מנהל בקבוצה.</b>
(יש להוסיף אותי כמנהל)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}

}
if($isadmin != true){
$txtbot = "<b>אינך מנהל או יוצר בקבוצה.</b>
רק מנהלים יכולים להוסיף את הקבוצה לבסיס נתונים!";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}



}







}

    #[FilterCommandCaseInsensitive('remove')]
    public function removegroupCommand(Incoming & GroupMessage  $message): void
    {
$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;

$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "(null)";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "(null)";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "(null)";
}

$usernames = $User_Full['User']['usernames']?? null;
if($usernames != null){
$usernames = implode(" , ", $usernames);
}
if($usernames == null){
$usernames = null;
}

$me = $this->getSelf();
$me_name = $me['first_name'];
$me_id = $me['id'];

$Chat_Full = $this->getInfo($message->chatId);
$title = $Chat_Full['Chat']['title']?? null;
if($title == null){
$title = "(null)";
}

$admrgh = $Chat_Full['Chat']['admin_rights']['ban_users']?? null;

$type = $Chat_Full['type'];

if($type != "supergroup"){
$txtbot = "<b>אני פועל רק בקבוצות-על(supergroup)</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}


if($type == "supergroup"){

$channelpart = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $message->senderId ]);
if(isset($channelpart['participant']['_'])&& ($channelpart['participant']['_'] == 'channelParticipantAdmin' or $channelpart['participant']['_'] == 'channelParticipantCreator'))  $isadmin = true;
else $isadmin = false;

if($isadmin != false){
	
$channelpart2 = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $me_id ]);
if(isset($channelpart2['participant']['_'])&& ($channelpart2['participant']['_'] == 'channelParticipantAdmin' or $channelpart2['participant']['_'] == 'channelParticipantCreator'))  $isadmin2 = true;
else $isadmin2 = false;	

if($isadmin2 != false){

if($admrgh == null){
$txtbot = "<b>אין לי הרשאות ניהול מתאימות.</b>
(הרשאות לחסימת משתמשים ושינוי הרשאות)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if($admrgh != null){
	
	



if (file_exists("data/DBgroups.txt")) {
$filex = Amp\File\read("data/DBgroups.txt");  
$user1 = explode("\n",$filex);
if(in_array($chatid,$user1)){
$filex = Amp\File\read("data/DBgroups.txt");  
$chatidstring = (string) $chatid;
$result = str_replace($chatidstring,"",$filex);
Amp\File\write("data/DBgroups.txt", $result);

$filex2 = Amp\File\read("data/DBgroups.txt");  
$result2 = preg_replace('/^[ \t]*[\r\n]+/m', '', $filex2);
Amp\File\write("data/DBgroups.txt", $result2);

$txtbot = "<b>הקבוצה הוסרה בהצלחה! אני עוזב את הקבוצה...</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
$this->channels->leaveChannel(channel: $message->chatId );


if (file_exists("data/$chatidstring/alertshabat.txt")) {
unlink("data/$chatidstring/alertshabat.txt");
}
if (file_exists("data/$chatidstring/msgclosermotan.txt")) {
unlink("data/$chatidstring/msgclosermotan.txt");
}


}
if(!in_array($chatid,$user1)){
$txtbot = "<b>הקבוצה כבר הוסרה מבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}	
}	

if (!file_exists("data/DBgroups.txt")) {
$txtbot = "<b>הקבוצה כבר הוסרה מבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}


}
}
if($isadmin2 != true){
$txtbot = "<b>אני לא מנהל בקבוצה.</b>
(יש להוסיף אותי כמנהל)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}

}
if($isadmin != true){
$txtbot = "<b>אינך מנהל או יוצר בקבוצה.</b>
רק מנהלים יכולים להוסיף את הקבוצה לבסיס נתונים!";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}



}







}

    #[FilterCommandCaseInsensitive('settings')]
    public function grupsettingsCommand(Incoming & GroupMessage  $message): void
    {
$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;

$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "(null)";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "(null)";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "(null)";
}

$me = $this->getSelf();
$me_name = $me['first_name'];
$me_id = $me['id'];

$Chat_Full = $this->getInfo($message->chatId);
$title = $Chat_Full['Chat']['title']?? null;
if($title == null){
$title = "(null)";
}

$admrgh = $Chat_Full['Chat']['admin_rights']['ban_users']?? null;

$type = $Chat_Full['type'];

if($type != "supergroup"){
$txtbot = "<b>אני פועל רק בקבוצות-על(supergroup)</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}


if($type == "supergroup"){

$channelpart = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $message->senderId ]);
if(isset($channelpart['participant']['_'])&& ($channelpart['participant']['_'] == 'channelParticipantAdmin' or $channelpart['participant']['_'] == 'channelParticipantCreator'))  $isadmin = true;
else $isadmin = false;

if($isadmin != false){
	
$channelpart2 = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $me_id ]);
if(isset($channelpart2['participant']['_'])&& ($channelpart2['participant']['_'] == 'channelParticipantAdmin' or $channelpart2['participant']['_'] == 'channelParticipantCreator'))  $isadmin2 = true;
else $isadmin2 = false;	

if($isadmin2 != false){

if($admrgh == null){
$txtbot = "<b>אין לי הרשאות ניהול מתאימות.</b>
(הרשאות לחסימת משתמשים ושינוי הרשאות)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if($admrgh != null){

if (file_exists("data/DBgroups.txt")) {
$filex = Amp\File\read("data/DBgroups.txt");  
$user1 = explode("\n",$filex);
if(!in_array($chatid,$user1)){
$txtbot = "<b>הקבוצה לא נוספה לבסיס נתונים!</b>
שלח את הפקודה <code>/add</code>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if(in_array($chatid,$user1)){
$txtbot2 = "<b>התאם אישית את הרובוט בקבוצה:</b>";
if (!file_exists("data/$chatid/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"שלחזמני"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (file_exists("data/$chatid/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"שלחזמני1"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (!file_exists("data/$chatid/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"הודעותלפניואחרי"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
if (file_exists("data/$chatid/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"הודעותלפניואחרי1"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח לפני שבת ✏️",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח במוצאי שבת ✏️",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"↪️ החזר לברירת מחדל",'callback_data'=>"החזרברירתמחדל"]];
$bot_API_markup[] = [['text'=>"סגור ✖️",'callback_data'=>"סגור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$this->messages->sendMessage(peer: $senderid, message: "$txtbot2", reply_markup: $bot_API_markup, parse_mode: 'HTML');
$txtbot = "<b>פאנל ההגדרות נשלח אליך בהודעה פרטית.</b>";
$bot_API_markup2[] = [['text'=>"לחץ כאן למעבר ⚙️",'url'=>"https://t.me/ShabatRobot"]];
$bot_API_markup2 = [ 'inline_keyboard'=> $bot_API_markup2,];
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", reply_markup: $bot_API_markup2, parse_mode: 'HTML');
Amp\File\write("data/$senderid/groupid.txt", "$chatid");
}
}

if (!file_exists("data/DBgroups.txt")) {
$txtbot = "<b>הקבוצה לא נוספה לבסיס נתונים!</b>
שלח את הפקודה <code>/add</code>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
}
}
if($isadmin2 != true){
$txtbot = "<b>אני לא מנהל בקבוצה.</b>
(יש להוסיף אותי כמנהל)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}

}
if($isadmin != true){
$txtbot = "<b>אינך מנהל או יוצר בקבוצה.</b>
רק מנהלים יכולים לפתוח פאנל הגדרות!";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}



}







}

   #[FilterButtonQueryData('חזרהלהגדרות')]
public function hazarasettigscommand(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
}

$txtbot = "<b>התאם אישית את הרובוט בקבוצה:</b>";
if (!file_exists("data/$filex/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"שלחזמני"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (file_exists("data/$filex/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"שלחזמני1"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (!file_exists("data/$filex/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"הודעותלפניואחרי"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
if (file_exists("data/$filex/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"הודעותלפניואחרי1"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח לפני שבת ✏️",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח במוצאי שבת ✏️",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"↪️ החזר לברירת מחדל",'callback_data'=>"החזרברירתמחדל"]];
$bot_API_markup[] = [['text'=>"סגור ✖️",'callback_data'=>"סגור"]];
//$bot_API_markup[] = [['text'=>"הגדרות זמני סגירה ⌚️",'callback_data'=>"לאפעילכרגע"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

   #[FilterButtonQueryData('החזרברירתמחדל')]
public function ahzerbriracommand(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
if (file_exists("data/$filex/alertshabat.txt")) {
unlink("data/$filex/alertshabat.txt");
}
if (file_exists("data/$filex/alertshabat2.txt")) {
unlink("data/$filex/alertshabat2.txt");
}
if (file_exists("data/$filex/msgclosermotan2.txt")) {
unlink("data/$filex/msgclosermotan2.txt");
}
if (file_exists("data/$filex/msgclosermotan.txt")) {
unlink("data/$filex/msgclosermotan.txt");
}
}
$txtbot = "<b>ההגדרות אופסו לברירת מחדל</b> ⚙️";
$bot_API_markup[] = [['text'=>"חזרה להגדרות",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

 #[FilterButtonQueryData('הסברזמנישבת')]
public function kiwitch1command(callbackQuery $query)
{
$query->answer($message = "האם הקבוצה תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת!", $alert = true, $url = null, $cacheTime = 0);		
}

 #[FilterButtonQueryData('הסברהודעותלפאח')]
public function kiwitch143command(callbackQuery $query)
{
$query->answer($message = "האם הקבוצה תקבל מידי יום שישי הודעה שתשלח בערב שבת ובצאת שבת!
• ניתן להשתמש בברירת מחדל.
• וניתן להגדיר הודעה מותאמת אישית.", $alert = true, $url = null, $cacheTime = 0);		
}

 #[FilterButtonQueryData('שלחזמני')]
public function kiwitchzmani1command(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
Amp\File\write("data/$filex/alertshabat.txt", "on");
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$query->editText($message = "<b>הקבוצה תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת! ✅</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

  #[FilterButtonQueryData('שלחזמני1')]
public function kiwitchzmani11command(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
if (file_exists("data/$filex/alertshabat.txt")) {
unlink("data/$filex/alertshabat.txt");
}
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$query->editText($message = "<b>הקבוצה לא תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת! ❌</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

 #[FilterButtonQueryData('הודעותלפניואחרי')]
public function kiwitchzmani12command(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
Amp\File\write("data/$filex/alertshabat2.txt", "on");
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>הקבוצה תקבל מידי יום שישי הודעה שתשלח בערב שבת ובצאת שבת!</b> ✅", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

  #[FilterButtonQueryData('הודעותלפניואחרי1')]
public function kiwitchzmani11command1(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
if (file_exists("data/$filex/alertshabat2.txt")) {
unlink("data/$filex/alertshabat2.txt");
}
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>הקבוצה לא תקבל מידי יום שישי הודעה שתשלח בערב שבת ובצאת שבת!</b> ❌", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

  #[FilterButtonQueryData('הודעתפתיחה')]
public function ptihahoda2command(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
if (file_exists("data/$filex/msgclosermotan2.txt")) {
$filex2 = Amp\File\read("data/$filex/msgclosermotan2.txt");  	
$CLOSER = self::OPENER;
$TXTSGRIGA = "<b>הוגדרה הודעת פתיחה ✔️</b>";
}
if (!file_exists("data/$filex/msgclosermotan2.txt")) {
$CLOSER = self::OPENER;
$TXTSGRIGA = "<b>לא הוגדרה הודעת פתיחה ✖️ </b>";
}
}

$bot_API_markup[] = [['text'=>"הצג הודעה 👁",'callback_data'=>"הצגהודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה ➕",'callback_data'=>"הגדרהודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"חזרה להגדרות",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כאן תוכל להגדיר הודעת פתיחה מותאמת אישית שתשלח במוצאי שבת כשהקבוצה נפתחת!
----------
$TXTSGRIGA
----------
ברירת מחדל: 
<code>$CLOSER</code>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
    if (file_exists("data/$userid/grs1.txt")) {
unlink("data/$userid/grs1.txt");
}
}

  #[FilterButtonQueryData('הודעתסגירה')]
public function ptihahodacommand(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
if (file_exists("data/$filex/msgclosermotan.txt")) {
$filex2 = Amp\File\read("data/$filex/msgclosermotan.txt");  	
$CLOSER = self::CLOSER;
$TXTSGRIGA = "<b>הוגדרה הודעת סגירה ✔️</b>";
}
if (!file_exists("data/$filex/msgclosermotan.txt")) {
$CLOSER = self::CLOSER;
$TXTSGRIGA = "<b>לא הוגדרה הודעת סגירה ✖️ </b>";
}
}

$bot_API_markup[] = [['text'=>"הצג הודעה 👁",'callback_data'=>"הצגהודעתסגירה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה ➕",'callback_data'=>"הגדרהודעתסגירה"]];
$bot_API_markup[] = [['text'=>"חזרה להגדרות",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כאן תוכל להגדיר הודעת סגירה מותאמת אישית שתשלח בערב שבת כשהקבוצה נסגרת!
----------
$TXTSGRIGA
----------
ברירת מחדל: 
<code>$CLOSER</code>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
    if (file_exists("data/$userid/grs1.txt")) {
unlink("data/$userid/grs1.txt");
}
}

  #[FilterButtonQueryData('הצגהודעתפתיחה')]
public function ptihahodahazteg2command(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
}


if (!file_exists("data/$filex/msgclosermotan2.txt")) {
$query->answer($message = "לא הוגדרה הודעת פתיחה ✖️", $alert = true, $url = null, $cacheTime = 0);
}
if (file_exists("data/$filex/msgclosermotan2.txt")) {
$txtcloser = Amp\File\read("data/$filex/msgclosermotan2.txt"); 

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtcloser", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

}
 
  #[FilterButtonQueryData('הצגהודעתסגירה')]
public function ptihahodahaztegcommand(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/$userid/groupid.txt")) {
$filex = Amp\File\read("data/$userid/groupid.txt");  
}


if (!file_exists("data/$filex/msgclosermotan.txt")) {
$query->answer($message = "לא הוגדרה הודעת סגירה ✖️", $alert = true, $url = null, $cacheTime = 0);
}
if (file_exists("data/$filex/msgclosermotan.txt")) {
$txtcloser = Amp\File\read("data/$filex/msgclosermotan.txt"); 

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtcloser", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

}

  #[FilterButtonQueryData('הגדרהודעתפתיחה')]
public function ptihahodahazteg12command(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>שלח לי את הודעת הפתיחה שתרצה להגדיר:</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
$msgqutryid = $query->messageId;
Amp\File\write("data/$userid/messagetodelete.txt", "$msgqutryid");
Amp\File\write("data/$userid/grs1.txt", 'txtsgira2');

}

  #[FilterButtonQueryData('הגדרהודעתסגירה')]
public function ptihahodahazteg1command(callbackQuery $query)
{
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>שלח לי את הודעת הסגירה שתרצה להגדיר:</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
$msgqutryid = $query->messageId;
Amp\File\write("data/$userid/messagetodelete.txt", "$msgqutryid");
Amp\File\write("data/$userid/grs1.txt", 'txtsgira');

}
 
    #[Handler] 
    public function handlemediax(Incoming & PrivateMessage $message): void
    {
$messagetext = $message->message;
$entities = $message->entities;
$messagefile = $message->media;
$messageid = $message->id;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}



    if (file_exists("data/$senderid/grs1.txt")) {
$mediax1 = Amp\File\read("data/$senderid/grs1.txt");    

if($mediax1 == "txtsgira"){
    
if(!preg_match('/^\/([Ss]tart)/',$messagetext)){   
 
if($messagetext == null){
$message->reply(message: "<b>נא לשלוח הודעת טקסט בלבד!</b>", parseMode: ParseMode::HTML);
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
}

if($messagetext != null){
$messageLength = mb_strlen($messagetext);

if($messageLength > 1024) {
$message->reply(message: "<b>נא לשלוח טקסט עד 1024 תווים</b>
כמות התווים ששלחת: $messageLength", parseMode: ParseMode::HTML);
} 
else 
{
unlink("data/$senderid/grs1.txt");

if (file_exists("data/$senderid/groupid.txt")) {
$filex = Amp\File\read("data/$senderid/groupid.txt");  
if (file_exists("data/$filex/msgclosermotan.txt")) {
unlink("data/$filex/msgclosermotan.txt");  	
}
$htmlmessage = $this->entitiesToHtml($messagetext, $entities, true);
Amp\File\write("data/$filex/msgclosermotan.txt", "$htmlmessage");
}

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"הודעתסגירה"]
        ]
    ]
];
$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "<b>הודעת הסגירה הוגדרה!</b> ✔️", reply_markup: $bot_API_markup, parse_mode: 'HTML');

$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
 if (file_exists("data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read("data/$senderid/messagetodelete.txt");  
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
unlink("data/$senderid/messagetodelete.txt");
}

}


	
}


}



}

if($mediax1 == "txtsgira2"){
    
if(!preg_match('/^\/([Ss]tart)/',$messagetext)){   
 
if($messagetext == null){
$message->reply(message: "<b>נא לשלוח הודעת טקסט בלבד!</b>", parseMode: ParseMode::HTML);
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
}

if($messagetext != null){
$messageLength = mb_strlen($messagetext);

if($messageLength > 1024) {
$message->reply(message: "<b>נא לשלוח טקסט עד 1024 תווים</b>
כמות התווים ששלחת: $messageLength", parseMode: ParseMode::HTML);
} 
else 
{
unlink("data/$senderid/grs1.txt");

if (file_exists("data/$senderid/groupid.txt")) {
$filex = Amp\File\read("data/$senderid/groupid.txt");  
if (file_exists("data/$filex/msgclosermotan2.txt")) {
unlink("data/$filex/msgclosermotan2.txt");  	
}
$htmlmessage = $this->entitiesToHtml($messagetext, $entities, true);
Amp\File\write("data/$filex/msgclosermotan2.txt", "$htmlmessage");
}

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"הודעתפתיחה"]
        ]
    ]
];
$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "<b>הודעת הפתיחה הוגדרה!</b> ✔️", reply_markup: $bot_API_markup, parse_mode: 'HTML');

$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
 if (file_exists("data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read("data/$senderid/messagetodelete.txt");  
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
unlink("data/$senderid/messagetodelete.txt");
}

}


	
}


}



}

}
}

    #[FilterCommandCaseInsensitive('stats')]
    public function statspubcommand(Incoming $message): void
    {
$chatid = $message->chatId;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}

$sentMessage = $this->messages->sendMessage(peer: $message->chatId, message: "<b>אנא המתן... מחשב</b> 📊", parse_mode: 'HTML');	
$sentMessage2 = $this->extractMessageId($sentMessage); 

try {

$dialogs = $this->getDialogIds();
$numFruits = count($dialogs);

$peerList31 = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "supergroup"){
continue;
}
$peerList31[]=$peer;
$numFruits31 = count($peerList31);
}

$peerList312 = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "chat"){
continue;
}
$peerList312[]=$peer;
$numFruits312 = count($peerList312);
}

if (!isset($numFruits312)) {
$numFruits312 = 0;
} else {
}
if (!isset($numFruits31)) {
$numFruits31 = 0;
} else {
}

$numFruits3new = $numFruits312 + $numFruits31;
$this->messages->editMessage(peer: $message->chatId, id: $sentMessage2, message: "<b>📊 סך הכל קבוצות שומרות שבת בזכותי:</b> <code>$numFruits3new</code>", parse_mode: 'HTML');

}catch (\danog\MadelineProto\Exception $e) {
} catch (\danog\MadelineProto\RPCErrorException $e) {
}
 
}

    #[Cron(period: 60.0)] 
    public function cron2(): void
    {
date_default_timezone_set("Asia/Jerusalem");
$TIME = date('H:i');
$DATE = date('d/m/Y');
$today = date("d/m/Y H:i"); 

if (file_exists("systemtimein.txt")) {
$systemtime = Amp\File\read("systemtimein.txt");


$dateTime = DateTime::createFromFormat('H:i', $systemtime);
$dateTime->sub(new DateInterval('PT10M'));
$formattedTime = $dateTime->format('H:i');
}
if (file_exists("systemdatein.txt")) {
$systemdate = Amp\File\read("systemdatein.txt");
}

$TIMER = "$today";
$TIMETOCHECK1 = "$systemdate 13:30";
if($TIMER == $TIMETOCHECK1 ){

if (file_exists("data/DBgroups.txt")) {
$userstoasend = Amp\File\read("data/DBgroups.txt");  
$usersArray = explode("\n", $userstoasend);
$usersArray = array_filter($usersArray);
$userstoasend1 = ($usersArray);

$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}

$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);

if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);


$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ <u><b>זמני כניסת ויציאת השבת:</b></u>

🗓 $updatedLine3$dateshabatout

🕯 <u>זמני כניסת השבת:</u>
ירושלים: <code>$timein</code>
חיפה: <code>$timeinzman2</code>
תל אביב: <code>$timeinzman3</code>
באר שבע: <code>$timeinzman4</code>

🍷 <u>זמני יציאת השבת:</u>
ירושלים: <code>$timeout</code>
חיפה: <code>$timeoutzman2</code>
תל אביב: <code>$timeoutzman3</code>
באר שבע: <code>$timeoutzman4</code>"; 

foreach ($userstoasend1 as $peer) {
if (file_exists("data/$peer/alertshabat.txt")) {
$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"מבית הישראלים 🇮🇱",'url'=>"https://t.me/the_israelis"]					
        ]
    ]
];
try {
$sendmoadaa1 = $this->messages->sendMessage(peer: $peer, message: $zmanim, reply_markup: $bot_API_markup, parse_mode: 'html');

}catch (\danog\MadelineProto\Exception $e) {
continue;
} catch (\danog\MadelineProto\RPCErrorException $e) {
continue;


}

}
}



}







}

$TIMER = "$today";
$TIMETOCHECK2 = "$systemdate $formattedTime";
if($TIMER == $TIMETOCHECK2 ){


if (file_exists("data/DBgroups.txt")) {
$userstoasend = Amp\File\read("data/DBgroups.txt");  
$usersArray = explode("\n", $userstoasend);
$usersArray = array_filter($usersArray);
$userstoasend1 = ($usersArray);



foreach ($userstoasend1 as $peer) {
try {
$info = $this->getInfo($peer);
$checkar1 = $info['Chat']['default_banned_rights']['view_messages'];
$checkar2 = $info['Chat']['default_banned_rights']['send_messages'];
$checkar3 = $info['Chat']['default_banned_rights']['send_media'];
$checkar4 = $info['Chat']['default_banned_rights']['send_stickers'];
$checkar5 = $info['Chat']['default_banned_rights']['send_gifs'];
$checkar6 = $info['Chat']['default_banned_rights']['send_games'];
$checkar7 = $info['Chat']['default_banned_rights']['send_inline'];
$checkar8 = $info['Chat']['default_banned_rights']['embed_links'];
$checkar9 = $info['Chat']['default_banned_rights']['send_polls'];
$checkar10 = $info['Chat']['default_banned_rights']['change_info'];
$checkar11 = $info['Chat']['default_banned_rights']['invite_users'];
$checkar12 = $info['Chat']['default_banned_rights']['pin_messages'];
$checkar13 = $info['Chat']['default_banned_rights']['manage_topics'];
$checkar14 = $info['Chat']['default_banned_rights']['send_photos'];
$checkar15 = $info['Chat']['default_banned_rights']['send_videos'];
$checkar16 = $info['Chat']['default_banned_rights']['send_roundvideos'];
$checkar17 = $info['Chat']['default_banned_rights']['send_audios'];
$checkar18 = $info['Chat']['default_banned_rights']['send_voices'];
$checkar19 = $info['Chat']['default_banned_rights']['send_docs'];
$checkar20 = $info['Chat']['default_banned_rights']['send_plain'];
$checkartime = $info['Chat']['default_banned_rights']['until_date'];
if($checkar1 != false){
$checkar1 = "true";
}else{
$checkar1 = "false";
}
if($checkar2 != false){
$checkar2 = "true";
}else{
$checkar2 = "false";
}
if($checkar3 != false){
$checkar3 = "true";
}else{
$checkar3 = "false";
}
if($checkar4 != false){
$checkar4 = "true";
}else{
$checkar4 = "false";
}
if($checkar5 != false){
$checkar5 = "true";
}else{
$checkar5 = "false";
}
if($checkar6 != false){
$checkar6 = "true";
}else{
$checkar6 = "false";
}
if($checkar7 != false){
$checkar7 = "true";
}else{
$checkar7 = "false";
}
if($checkar8 != false){
$checkar8 = "true";
}else{
$checkar8 = "false";
}
if($checkar9 != false){
$checkar9 = "true";
}else{
$checkar9 = "false";
}
if($checkar10 != false){
$checkar10 = "true";
}else{
$checkar10 = "false";
}
if($checkar11 != false){
$checkar11 = "true";
}else{
$checkar11 = "false";
}
if($checkar12 != false){
$checkar12 = "true";
}else{
$checkar12 = "false";
}
if($checkar13 != false){
$checkar13 = "true";
}else{
$checkar13 = "false";
}
if($checkar14 != false){
$checkar14 = "true";
}else{
$checkar14 = "false";
}
if($checkar15 != false){
$checkar15 = "true";
}else{
$checkar15 = "false";
}
if($checkar16 != false){
$checkar16 = "true";
}else{
$checkar16 = "false";
}
if($checkar17 != false){
$checkar17 = "true";
}else{
$checkar17 = "false";
}
if($checkar18 != false){
$checkar18 = "true";
}else{
$checkar18 = "false";
}
if($checkar19 != false){
$checkar19 = "true";
}else{
$checkar19 = "false";
}
if($checkar20 != false){
$checkar20 = "true";
}else{
$checkar20 = "false";
}
$checkartime20 = (string) $checkartime;

Amp\File\write("data/$peer/chatb1.txt",$checkar1."\n".$checkar2."\n".$checkar3."\n".$checkar4."\n".$checkar5."\n".$checkar6."\n".$checkar7."\n".$checkar8."\n".$checkar9."\n".$checkar10."\n".$checkar11."\n".$checkar12."\n".$checkar13."\n".$checkar14."\n".$checkar15."\n".$checkar16."\n".$checkar17."\n".$checkar18."\n".$checkar19."\n".$checkar20."\n".$checkartime20);

$chatBannedRights = ['_'                => 'chatBannedRights', 
                    'view_messages'     => false, 
                    'send_messages'     => true, 
                    'send_media'        => true, 
                    'send_stickers'     => true, 
                    'send_gifs'         => true, 
                    'send_games'        => true, 
                    'send_inline'       => true, 
                    'embed_links'       => true, 
                    'send_polls'        => true, 
                    'change_info'       => true, 
                    'invite_users'      => true, 
                    'pin_messages'      => true,
                    'manage_topics'     => true, 
                    'send_photos'       => true, 
                    'send_videos'       => true, 
                    'send_roundvideos'  => true, 
                    'send_audios'       => true, 
                    'send_voices'       => true, 
                    'send_docs'         => true,
                    'send_plain'        => true, 
                    'until_date'        => 0,
                ];
	
$Updates1 = $this->messages->editChatDefaultBannedRights(peer: $peer, banned_rights: $chatBannedRights, );

if (file_exists("data/$peer/alertshabat2.txt")) {
if (file_exists("data/$peer/msgclosermotan.txt")) {
$modaha = Amp\File\read("data/$peer/msgclosermotan.txt");  	
}
if (!file_exists("data/$peer/msgclosermotan.txt")) {
$modaha = self::CLOSER;
}

$sendmoadaa1 = $this->messages->sendMessage(peer: $peer, message: $modaha, parse_mode: 'html');
}
$this->sleep(0.1);

}catch (\danog\MadelineProto\Exception $e) {
continue;
} catch (\danog\MadelineProto\RPCErrorException $e) {
continue;
} 
}



}








}

if (file_exists("systemtimeout.txt")) {
$systemtime2 = Amp\File\read("systemtimeout.txt");


$dateTime2 = DateTime::createFromFormat('H:i', $systemtime2);
$dateTime2->sub(new DateInterval('PT0M'));
$formattedTime2 = $dateTime2->format('H:i');
}
if (file_exists("systemdateout.txt")) {
$systemdate2 = Amp\File\read("systemdateout.txt");
}

$TIMER = "$today";
$TIMETOCHECK3 = "$systemdate2 $formattedTime2";
if($TIMER == $TIMETOCHECK3 ){

if (file_exists("data/DBgroups.txt")) {
$userstoasend = Amp\File\read("data/DBgroups.txt");  
$usersArray = explode("\n", $userstoasend);
$usersArray = array_filter($usersArray);
$userstoasend1 = ($usersArray);

foreach ($userstoasend1 as $peer) {
try {

if (file_exists("data/$peer/chatb1.txt")) {
$lines = file("data/$peer/chatb1.txt");
$dillerr1 = $lines[0];
$dillerr2 = $lines[1];
$dillerr3 = $lines[2];
$dillerr4 = $lines[3];
$dillerr5 = $lines[4];
$dillerr6 = $lines[5];
$dillerr7 = $lines[6];
$dillerr8 = $lines[7];
$dillerr9 = $lines[8];
$dillerr10 = $lines[9];
$dillerr11 = $lines[10];
$dillerr12 = $lines[11];
$dillerr13 = $lines[12];
$dillerr14 = $lines[13];
$dillerr15 = $lines[14];
$dillerr16 = $lines[15];
$dillerr17 = $lines[16];
$dillerr18 = $lines[17];
$dillerr19 = $lines[18];
$dillerr20 = $lines[19];
$dillerr21 = $lines[20];

$checkarnew1 = filter_var($dillerr1, FILTER_VALIDATE_BOOLEAN);
$checkarnew2 = filter_var($dillerr2, FILTER_VALIDATE_BOOLEAN);
$checkarnew3 = filter_var($dillerr3, FILTER_VALIDATE_BOOLEAN);
$checkarnew4 = filter_var($dillerr4, FILTER_VALIDATE_BOOLEAN);
$checkarnew5 = filter_var($dillerr5, FILTER_VALIDATE_BOOLEAN);
$checkarnew6 = filter_var($dillerr6, FILTER_VALIDATE_BOOLEAN);
$checkarnew7 = filter_var($dillerr7, FILTER_VALIDATE_BOOLEAN);
$checkarnew8 = filter_var($dillerr8, FILTER_VALIDATE_BOOLEAN);
$checkarnew9 = filter_var($dillerr9, FILTER_VALIDATE_BOOLEAN);
$checkarnew10 = filter_var($dillerr10, FILTER_VALIDATE_BOOLEAN);
$checkarnew11 = filter_var($dillerr11, FILTER_VALIDATE_BOOLEAN);
$checkarnew12 = filter_var($dillerr12, FILTER_VALIDATE_BOOLEAN);
$checkarnew13 = filter_var($dillerr13, FILTER_VALIDATE_BOOLEAN);
$checkarnew14 = filter_var($dillerr14, FILTER_VALIDATE_BOOLEAN);
$checkarnew15 = filter_var($dillerr15, FILTER_VALIDATE_BOOLEAN);
$checkarnew16 = filter_var($dillerr16, FILTER_VALIDATE_BOOLEAN);
$checkarnew17 = filter_var($dillerr17, FILTER_VALIDATE_BOOLEAN);
$checkarnew18 = filter_var($dillerr18, FILTER_VALIDATE_BOOLEAN);
$checkarnew19 = filter_var($dillerr19, FILTER_VALIDATE_BOOLEAN);
$checkarnew20 = filter_var($dillerr20, FILTER_VALIDATE_BOOLEAN);
$checkarnew21 = intval($dillerr21);

$chatBannedRights2 = ['_'                => 'chatBannedRights', 
                    'view_messages'     => $checkarnew1,
                    'send_messages'     => $checkarnew2, 
                    'send_media'        => $checkarnew3, 
                    'send_stickers'     => $checkarnew4, 
                    'send_gifs'         => $checkarnew5, 
                    'send_games'        => $checkarnew6, 
                    'send_inline'       => $checkarnew7, 
                    'embed_links'       => $checkarnew8, 
                    'send_polls'        => $checkarnew9, 
                    'change_info'       => $checkarnew10, 
                    'invite_users'      => $checkarnew11, 
                    'pin_messages'      => $checkarnew12,
                    'manage_topics'     => $checkarnew13, 
                    'send_photos'       => $checkarnew14, 
                    'send_videos'       => $checkarnew15, 
                    'send_roundvideos'  => $checkarnew16, 
                    'send_audios'       => $checkarnew17, 
                    'send_voices'       => $checkarnew18, 
                    'send_docs'         => $checkarnew19,
                    'send_plain'        => $checkarnew20, 
                    'until_date'        => $checkarnew21,
                ];

$Updates2 = $this->messages->editChatDefaultBannedRights(peer: $peer, banned_rights: $chatBannedRights2, );
if (file_exists("data/$peer/alertshabat2.txt")) {
if (file_exists("data/$peer/msgclosermotan2.txt")) {
$modaha = Amp\File\read("data/$peer/msgclosermotan2.txt");  	
}
if (!file_exists("data/$peer/msgclosermotan2.txt")) {
$modaha = self::OPENER;
}

$sendmoadaa1 = $this->messages->sendMessage(peer: $peer, message: $modaha, parse_mode: 'html');
}
$this->sleep(0.1);
}
}catch (\danog\MadelineProto\Exception $e) {
continue;
} catch (\danog\MadelineProto\RPCErrorException $e) {
continue;
}
}


}









}

$TIMER1 = "$DATE 00:01";
if($today == $TIMER1){

$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
}
$result11 = trim($result11);
$result21 = trim($result21);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);
if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);
$resultline4new89 = strstr($result11, ',');
$resultline4new89 = str_ireplace(",", "", $resultline4new89);
if (preg_match('/^\s/', $resultline4new89)) {
$resultline4new89 = ltrim($resultline4new89, ' ');
}
$resultline4new89 = rtrim($resultline4new89);

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);

preg_match_all('!\d+!', $resultline4new89, $matches);
$numbers2 = implode(' ', $matches[0]);

$numbersin = $numbers2;
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$messageLength = mb_strlen((string)$numbersin);
if($messageLength == 1){
$numbersin = "0".$numbersin;
}

$lastTwoDigits = date('Y');

$dateshabatin = "$numbersin/$hodesh/$lastTwoDigits";
$timeshabatin = $timein;

$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";
$timeshabatout = $timeout;

Amp\File\write("systemtimein.txt",$timeshabatin);
Amp\File\write("systemdatein.txt",$dateshabatin);
Amp\File\write("systemtimeout.txt",$timeshabatout);
Amp\File\write("systemdateout.txt",$dateshabatout);
}

}


//////////////////////// ADMIN COMMANDS

#[FilterCommandCaseInsensitive('admin')]
public function admincommand(Incoming & PrivateMessage & FromAdmin $message): void
    {

$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}


$bot_API_markup[] = [['text'=>"סטטיסטיקות מנויים 📊",'callback_data'=>"סטטיסטיקות"]];
$bot_API_markup[] = [['text'=>"הצג מנויים 👁",'callback_data'=>"רשימתמשתמשים"]];
$bot_API_markup[] = [['text'=>"שידור למנויים 📮",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup[] = [['text'=>"ייצוא נתונים 📤",'callback_data'=>"ייצואנתונים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->messages->sendMessage(peer: $message->senderId, message: "<b>ברוך הבא מנהל! 👋</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');
    if (file_exists("data/$senderid/grs1.txt")) {
unlink("data/$senderid/grs1.txt");
}
    }

#[FilterButtonQueryData('חזרהמנהל')] 
public function addsohe1hazor(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}


$bot_API_markup[] = [['text'=>"סטטיסטיקות מנויים 📊",'callback_data'=>"סטטיסטיקות"]];
$bot_API_markup[] = [['text'=>"הצג מנויים 👁",'callback_data'=>"רשימתמשתמשים"]];
$bot_API_markup[] = [['text'=>"שידור למנויים 📮",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup[] = [['text'=>"ייצוא נתונים 📤",'callback_data'=>"ייצואנתונים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];


$query->editText($message = "<b>ברוך הבא מנהל! 👋</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
if (file_exists("data/$userid/grs1.txt")) {
unlink("data/$userid/grs1.txt");
}
}

#[FilterButtonQueryData('סטטיסטיקות')] 
public function statsusers(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
   $bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];

$query->editText($message = "<b>אנא המתן... מחשב 📊</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

$dialogs = $this->getDialogIds();
$numFruits = count($dialogs);

$peerList2 = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "channel"){
continue;
}
$peerList2[]=$peer;
$numFruits2 = count($peerList2);
}

$peerList31 = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "supergroup"){
continue;
}
$peerList31[]=$peer;
$numFruits31 = count($peerList31);
}

$peerList312 = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "chat"){
continue;
}
$peerList312[]=$peer;
$numFruits312 = count($peerList312);
}

if (!isset($numFruits312)) {
$numFruits312 = 0;
} else {
}
if (!isset($numFruits31)) {
$numFruits31 = 0;
} else {
}
if (!isset($numFruits2)) {
$numFruits2 = 0;
} else {
}


$numFruits3new = $numFruits2 + $numFruits312 + $numFruits31;
$numofall = $numFruits - $numFruits3new;


$query->editText($message = "<b>🧮 סטטיסטיקות מנויים 📊</b>
- - - - - - - - - -
כמות ערוצים: $numFruits2
כמות קבוצות: $numFruits312
כמות קבוצות-על: $numFruits31
כמות משתמשים: $numofall
- - - - - - - - - -
סך הכל מנויים: $numFruits", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

#[FilterButtonQueryData('רשימתמשתמשים')] 
public function reshimamishtamshim(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup1 = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];

$dialogs = $this->getDialogIds();
$newLangsComma = implode("\n", $dialogs);
Amp\File\write("ids.txt",$newLangsComma);
$filex = Amp\File\read("ids.txt");
$numFruits = count($dialogs);

if($filex != null){
$file = 'ids.txt';
$outputFile = "idsnew.txt"; 
$startLine = 1; 
$endLine = 50; 
$lines = file($file); 
$selectedLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);
Amp\File\write($outputFile, implode("", $selectedLines));
$outputFilex = Amp\File\read("idsnew.txt"); 


Amp\File\write("data/startline.txt","$startLine");
Amp\File\write("data/endline.txt","$endLine");


if($numFruits > $endLine){
$keyboardreshima[] = [['text'=>"הבא",'callback_data'=>"רשימתמנוייםהמשך"]];
}
if($startLine > 50){
$keyboardreshima[] = [['text'=>"הקודם",'callback_data'=>"רשימתמנוייםהקודם"]];
}
$keyboardreshima[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$keyboardreshima = [ 'inline_keyboard'=> $keyboardreshima,];


$query->editText($message = "$outputFilex
- - - - - - - - - -
סך הכל מנויים: $numFruits
*הרשימה כוללת קבוצות וערוצים", $replyMarkup = $keyboardreshima, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

if($filex == null){

$query->editText($message = "אין עדיין מנויים.", $replyMarkup = $bot_API_markup1, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}



}

#[FilterButtonQueryData('רשימתמנוייםהמשך')] 
public function reshimamishtamshim2(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup1 = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];

$dialogs = $this->getDialogIds();
$newLangsComma = implode("\n", $dialogs);
Amp\File\write("ids.txt",$newLangsComma);
$filex = Amp\File\read("ids.txt");
$numFruits = count($dialogs);

if($filex != null){
	
	
	
$startx = Amp\File\read("data/startline.txt"); 
$endx = Amp\File\read("data/endline.txt"); 
$startx = $startx + 50;
$endx = $endx + 50;
$file = 'ids.txt';
$outputFile = "idsnew.txt"; 
$startLine = $startx; 
$endLine = $endx; 
$lines = file($file); 
Amp\File\write("data/startline.txt","$startLine");
Amp\File\write("data/endline.txt","$endLine");
$selectedLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);
Amp\File\write($outputFile, implode("", $selectedLines)); 
$outputFilex = Amp\File\read("idsnew.txt"); 



if($numFruits > $endLine){
$keyboardreshima[] = [['text'=>"הבא",'callback_data'=>"רשימתמנוייםהמשך"]];
}
if($startLine > 50){
$keyboardreshima[] = [['text'=>"הקודם",'callback_data'=>"רשימתמנוייםהקודם"]];
}
$keyboardreshima[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$keyboardreshima = [ 'inline_keyboard'=> $keyboardreshima,];

$query->editText($message = "$outputFilex
- - - - - - - - - -
סך הכל מנויים: $numFruits
*הרשימה כוללת קבוצות וערוצים", $replyMarkup = $keyboardreshima, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

if($filex == null){

$query->editText($message = "אין עדיין מנויים.", $replyMarkup = $bot_API_markup1, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}



}

#[FilterButtonQueryData('רשימתמנוייםהקודם')] 
public function reshimamishtamshim3(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup1 = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];

$dialogs = $this->getDialogIds();
$newLangsComma = implode("\n", $dialogs);
Amp\File\write("ids.txt",$newLangsComma);
$filex = Amp\File\read("ids.txt");
$numFruits = count($dialogs);

if($filex != null){	
$startx = Amp\File\read("data/startline.txt"); 
$endx = Amp\File\read("data/endline.txt"); 
$startx = $startx - 50;
$endx = $endx - 50;
$file = 'ids.txt';
$outputFile = "idsnew.txt"; 
$startLine = $startx; 
$endLine = $endx; 
$lines = file($file); 
Amp\File\write("data/startline.txt","$startLine");
Amp\File\write("data/endline.txt","$endLine");
$selectedLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);
Amp\File\write($outputFile, implode("", $selectedLines)); 
$outputFilex = Amp\File\read("idsnew.txt"); 



if($numFruits > $endLine){
$keyboardreshima[] = [['text'=>"הבא",'callback_data'=>"רשימתמנוייםהמשך"]];
}
if($startLine > 50){
$keyboardreshima[] = [['text'=>"הקודם",'callback_data'=>"רשימתמנוייםהקודם"]];
}
$keyboardreshima[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$keyboardreshima = [ 'inline_keyboard'=> $keyboardreshima,];

$query->editText($message = "$outputFilex
- - - - - - - - - -
סך הכל מנויים: $numFruits
*הרשימה כוללת קבוצות וערוצים", $replyMarkup = $keyboardreshima, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}

if($filex == null){

$query->editText($message = "אין עדיין מנויים.", $replyMarkup = $bot_API_markup1, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}



}

#[FilterButtonQueryData('ייצואנתונים')] 
public function addtexexortdata(callbackQuery $query)
{
$userid = $query->userId;  
$msgid = $query->messageId;  
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
   $bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];
$query->editText($message = "<b>אנא המתן... מייצא נתונים</b> 📤", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

$dialogs = $this->getDialogIds();

$peerList = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
//if(!isset($info['type']) || $info['type'] == "bot"){
//continue;
//}

if(!isset($info['User']['username'])){
$userpeer = "NULL";
}
if(isset($info['User']['username'])){
$userpeer = $info['User']['username'];
}

if(isset($info['User']['first_name'])){
$namepeer = $info['User']['first_name'];
}
if(!isset($info['User']['first_name'])){
$namepeer = "NULL";
}

if(!isset($info['User']['phone'])){
$phoneeer = "NULL";
}
if(isset($info['User']['phone'])){
$phoneeer = $info['User']['phone'];
}

if(!isset($info['type'])){
$typepeer = "NULL";
}
if(isset($info['type'])){
$typepeer = $info['type'];
}

$users = "$peer, $namepeer, $typepeer, $userpeer, $phoneeer";
$peerList[]=$users;
$numFruits = count($peerList);
}
$typescsv = "ID, NAME, TYPE, USERNAME, PHONE";
$newLangsComma = implode("\n", $peerList);
Amp\File\write("users.csv",$typescsv."\n".$newLangsComma);
$filex = Amp\File\read("users.csv");


if (file_exists("users.csv")) {
$file1 = new LocalFile('users.csv');
$sentMessage = $this->sendDocument(
    peer: $userid,
    file: $file1,
    caption: "*נתוני מנויים מערכת!*
- - - - - - - - - -
סך הכל מנויים: $numFruits",
    parseMode: ParseMode::MARKDOWN
);
}

$this->messages->deleteMessages(revoke: true, id: [$msgid]); 
$this->messages->sendMessage(peer: $userid, message: "<b>הנתונים נשלחו בהצלחה! ✔️</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');

}

#[FilterButtonQueryData('שידורלמשתמשים')] 
public function addsoheshidur1(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/broadcastsend.txt")) {
$broadcast_send = Amp\File\read("data/broadcastsend.txt");
}
if (!file_exists("data/broadcastsend.txt")) {
$broadcast_send = "כולם";
}


if (file_exists("data/setbroadforward.txt")) {
$bot_API_markup[] = [['text'=>"העבר הודעה(עם קרדיט): ✔️",'callback_data'=>"העברהודעהללא"]];
}
if (!file_exists("data/setbroadforward.txt")) {
$bot_API_markup[] = [['text'=>"העבר הודעה(עם קרדיט): ✖️",'callback_data'=>"העברהודעה"]];
}
$bot_API_markup[] = [['text'=>"תפוצה: $broadcast_send",'callback_data'=>"מצבתפוצה"]];
$bot_API_markup[] = [['text'=>"❌ ביטול ❌",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>נא שלח את ההודעה שתרצה לשלוח:</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

Amp\File\write("data/$userid/grs1.txt", 'broadcast');
$msgqutryid = $query->messageId;
Amp\File\write("data/$userid/messagetodelete.txt", "$msgqutryid");

}

#[FilterButtonQueryData('העברהודעה')] 
public function addsoheshidur1for(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write("data/setbroadforward.txt","on");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כעת ההודעה תשלח עם קרדיט ✔️", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('העברהודעהללא')] 
public function addsoheshidur1for2(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists("data/setbroadforward.txt")) {
unlink("data/setbroadforward.txt");
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כעת ההודעה תשלח ללא קרדיט ✖️", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה')] 
public function broadsetsenders(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"רק למשתמשים",'callback_data'=>"מצבתפוצה1"]];
$bot_API_markup[] = [['text'=>"רק לערוצים",'callback_data'=>"מצבתפוצה2"]];
$bot_API_markup[] = [['text'=>"רק לקבוצות",'callback_data'=>"מצבתפוצה3"]];
$bot_API_markup[] = [['text'=>"לכל המנויים",'callback_data'=>"מצבתפוצה4"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>אנא בחר מצב תפוצה 🔘</b>
האם לשלוח את ההודעה לכל המנויים(משתמשים קבוצות וערוצים) או עם פילטר: רק משתמשים/קבוצות/ערוצים.", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה1')] 
public function broadsetsenders1(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write("data/broadcastsend.txt","משתמשים");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> רק למשתמשים", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה2')] 
public function broadsetsenders2(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write("data/broadcastsend.txt","ערוצים");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> רק לערוצים", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה3')] 
public function broadsetsenders3(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write("data/broadcastsend.txt","קבוצות");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> רק לקבוצות", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה4')] 
public function broadsetsenders4(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write("data/broadcastsend.txt","כולם");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> כולם", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

    #[Handler]
    public function handlebroadcast(Incoming & PrivateMessage & FromAdmin $message): void
    {
$messagetext = $message->message;
$entities = $message->entities;
$messagefile = $message->media;
$messageid = $message->id;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}



    if (file_exists("data/$senderid/grs1.txt")) {
$check = Amp\File\read("data/$senderid/grs1.txt");    
if($check == "broadcast"){
    
if(!preg_match('/^\/([Ss]tart)/',$messagetext)){   

unlink("data/$senderid/grs1.txt"); 

if (file_exists("data/setbroadforward.txt")) {

    if (file_exists("data/broadcastsend.txt")) {
$check2 = Amp\File\read("data/broadcastsend.txt");    
if($check2 == "משתמשים"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: false,
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}
if($check2 == "ערוצים"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: false,
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}
if($check2 == "קבוצות"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: false,
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}
if($check2 == "כולם"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: false,
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}	
}

    if (!file_exists("data/broadcastsend.txt")) {
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: false,
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);




}

}

if (!file_exists("data/setbroadforward.txt")) {

    if (file_exists("data/broadcastsend.txt")) {
$check2 = Amp\File\read("data/broadcastsend.txt");    
if($check2 == "משתמשים"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: true,
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}
if($check2 == "ערוצים"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: true,
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}
if($check2 == "קבוצות"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: true,
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}
if($check2 == "כולם"){
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: true,
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}	
}

    if (!file_exists("data/broadcastsend.txt")) {
        $this->broadcastForwardMessages(
            from_peer: $message->senderId,
            message_ids: [$message->id],
            drop_author: true,
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);




}




}

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];
$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "📯 שולח את הודעת השידור..", reply_markup: $bot_API_markup);
$sentMessage2 = $this->extractMessageId($sentMessage);
Amp\File\write("data/messagetoeditbroadcast1.txt", "$sentMessage2");
Amp\File\write("data/messagetoeditbroadcast2.txt", "$senderid");



 if (file_exists("data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read("data/$senderid/messagetodelete.txt");  
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
unlink("data/$senderid/messagetodelete.txt");
}

}


	
}


}

}

    private int $lastLog = 0;

    #[Handler]
    public function handleBroadcastProgress(Progress $progress): void
    {
                if (time() - $this->lastLog > 5 || $progress->status === Status::GATHERING_PEERS) {
            $this->lastLog = time();


$progressStr = (string) $progress;

 if (file_exists("data/messagetoeditbroadcast2.txt")) {
$filexmsgid1 = Amp\File\read("data/messagetoeditbroadcast2.txt");  

 if (file_exists("data/messagetoeditbroadcast1.txt")) {
$filexmsgid2 = Amp\File\read("data/messagetoeditbroadcast1.txt");  

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];

$this->messages->editMessage(peer: $filexmsgid1, id: $filexmsgid2, message: "$progressStr", reply_markup: $bot_API_markup);
//unlink("data/messagetoeditbroadcast2.txt");
//unlink("data/messagetoeditbroadcast1.txt");
 }
 }
 
 
				}

        if (time() - $this->lastLog > 5 || $progress->status === Status::FINISHED) {
            $this->lastLog = time();
// $this->sendMessageToAdmins((string) $progress);
//$this->sendMessageToAdmins("✅ ההודעה נשלחה ל: $broadcast_send");


 if (file_exists("data/messagetoeditbroadcast2.txt")) {
$filexmsgid1 = Amp\File\read("data/messagetoeditbroadcast2.txt");  

 if (file_exists("data/messagetoeditbroadcast1.txt")) {
$filexmsgid2 = Amp\File\read("data/messagetoeditbroadcast1.txt");  
$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];

//if ($progress !== null) {
//    assert($progress instanceof Progress);

//    $progressStr = (string) $progress;

    $pendingCount = $progress->pendingCount;
    $sucessCount = $progress->successCount;
    $sucessCount2 = $progress->failCount;
//}

$this->messages->editMessage(peer: $filexmsgid1, id: $filexmsgid2, message: "✅ ההודעה נשלחה ל: $sucessCount
❌ נכשל בעת השליחה: $sucessCount2", reply_markup: $bot_API_markup);
//unlink("data/messagetoeditbroadcast2.txt");
//unlink("data/messagetoeditbroadcast1.txt");
 }
 }









        }
        }


}

$API_ID = parse_ini_file('.env')['API_ID'];
$API_HASH = parse_ini_file('.env')['API_HASH'];
$BOT_TOKEN = parse_ini_file('.env')['BOT_TOKEN'];
$settings = new Settings;
$settings->setAppInfo((new \danog\MadelineProto\Settings\AppInfo)->setApiId((int)$API_ID)->setApiHash($API_HASH));
MyEventHandler::startAndLoopBot('bot.madeline', $BOT_TOKEN, $settings);
