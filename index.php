<style>
    body{
        font-family: arial;
    }
</style>
<head>
    <title>Чатик с бэком</title>
    <link href="favicon-16.png" rel="icon" type="image/x-icon">    
</head>
<?php

date_default_timezone_set('Asia/Vladivostok');

$users = array (
"login"=>"password",
"admin"=>"admin"
);

$url = 'http://fefu.ml';

function print_messages(){
    $content = json_decode(file_get_contents('history.json'));
    foreach($content->messages as $message){
    echo("<b>$message->sender</b> $message->date<br>$message->text<br><br>");
    }
}

function add_message(){
    $message = json_decode(file_get_contents('history.json'), true);
    $message['messages'][] = [
    'text'=>$_GET['message'],
    'date'=>(new DateTimeImmutable())->format('H:i'),
    'sender'=>$_COOKIE['login']
    ];
    file_put_contents('history.json', json_encode($message));
}

function clear_history(){
    unlink('history.json');
    print_messages;
}

function reset_history(){
    clear_history();
    $content = json_decode(file_get_contents('reset.json'));
    file_put_contents('history.json', json_encode($content));
    print_messages();
}

$uri = $_SERVER['REQUEST_URI'];
switch ($uri){
case '/logout?':
    {
    setcookie('login', '');   
    header('Location: '.$url); 
    break;
    }
case '/clear?':
    {
    clear_history();
    header('Location: '.$url);
    break;
    }
case '/reset?':
    {
    reset_history();
    header('Location: '.$url);
    break;
    }
}
if (isset($_GET['login'])&&isset($_GET['password'])) {
    if ($users[$_GET['login']]==$_GET['password'] AND $users[$_GET['login']]!=""){
        setcookie('login', $_GET['login'], time() + 180);
        header('Location: '.$url);
    } else {
        ?>
        Неверный логин или пароль!<br><br>
        <?php
    }
}
if (!isset($_COOKIE['login'])):
?>

<form action="/" method="GET">
    <input placeholder="Логин" name="login" style="width: 250px; height: 40px"><br>
    <input placeholder="Пароль" name="password" type="password" style="width: 250px; height: 40px"><br>
    <input type="submit" value="Авторизоваться" style="width: 250px; height: 40px">
</form>
<form action="/reset" method="GET">
        <input type="submit" value="RESET MESSAGES" style="width: 250px; height: 40px; 
        right: 8px; top: 8px; position: absolute; background-color: red; color: white;font-weight: bold;">
</form>
<b>История сообщений:</b><br><br>

<?php else:?>
<form action="/send" method="GET">
    <input placeholder="Напишите сообщение, <?php echo($_COOKIE['login']);?>..." name="message" style="width: 250px; height: 40px">
    <input type="submit" value="Отправить" style="width: 250px; height: 40px; 
    margin-left:-5px;">
</form>
<form action="/reset" method="GET">
        <input type="submit" value="RESET MESSAGES" style="width: 250px; height: 40px; 
        right: 8px; top: 8px; position: absolute; background-color: red; color: white;font-weight: bold;">
</form>
<form action="/logout" method="GET">
    <input type="submit" value="Выход" style="width: 250px; height: 40px; margin-top:-16px;">
</form>
<form action="/clear" method="GET">
    <input type="submit" value="Очистить историю сообщений" style="width: 250px; height: 40px; margin-top:-16px;">
</form>

<b>История сообщений:</b><br><br>
<?php
endif;
if(isset($_GET['message']) AND $_GET['message'] != ''){
    add_message();
    header('Location: '.$url.'/send?message=');
}
print_messages();
?>
