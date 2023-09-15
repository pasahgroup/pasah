<?php require_once('header.php'); ?>

<?php
// After form submit checking everything for email sending
if(isset($_POST['form1']))
{   
    $statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
    $statement->execute();
    $result = $statement->fetchAll();                           
    foreach ($result as $row) {
        $send_email_from  = $row['send_email_from'];
        $receive_email_to = $row['receive_email_to'];
        $smtp_host        = $row['smtp_host'];
        $smtp_port        = $row['smtp_port'];
        $smtp_username    = $row['smtp_username'];
        $smtp_password    = $row['smtp_password'];
    }

    $valid = 1;

    if(empty($_POST['subject']))
    {
        $valid = 0;
        $error_message .= 'Subject can not be empty<br>';
    }

    if(empty($_POST['message']))
    {
        $valid = 0;
        $error_message .= 'Message can not be empty<br>';
    }

    if($valid == 1)
    {
        require_once '../vendor/autoload.php';

		$transport = (new Swift_SmtpTransport($smtp_host, $smtp_port))
		->setUsername($smtp_username)
		->setPassword($smtp_password);
		
        $statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_active=1");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach ($result as $row)
        {
            $mailer = new Swift_Mailer($transport);
            $message = (new Swift_Message($_POST['subject']))
                ->setFrom([$send_email_from])
                ->setTo([$row['subs_email']])
                ->setReplyTo([$receive_email_to])
                ->setBody($_POST['message'],'text/html');

		    $mailer->send($message);
        }
        
        $success_message = 'Email is sent successfully to all subscribers.';

    }
}
?>

<section class="content-header">
	<div class="content-header-left">
        <h1>Send Email to Subscriber</h1>
    </div>
    <div class="content-header-right">
        <a href="subscriber.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>


<section class="content">

    <div class="row">
        <div class="col-md-12">

            <?php if($error_message): ?>
            <div class="callout callout-danger">
            
            <p>
            <?php echo $error_message; ?>
            </p>
            </div>
            <?php endif; ?>

            <?php if($success_message): ?>
            <div class="callout callout-success">
            
            <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Subject </label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" class="form-control" name="subject">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Message </label>
                            <div class="col-sm-9">
                                <textarea class="form-control editor" name="message"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Send Email</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>



<?php require_once('footer.php'); ?>