<?php
/**
 * canvasAPIのphpバージョン
 */

header('Content-type: text/html; charset=UTF-8');

define("CONSUMER_SECRET", "input your app consumer secret");

//signed_requestが正しいかどうかを検証
$splits = mb_split("[.]", $_POST["signed_request"], 2);

$context = base64_decode($splits[1]);

$json = json_decode($context);

if (base64_encode(hash_hmac("sha256", $splits[1], CONSUMER_SECRET, true)) !== $splits[0]) {
	echo "コンテキストが不正です。";
	exit();
}
?>
<html>
	<head>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $json->client->instanceUrl; ?>/canvas/sdk/js/28.0/canvas-all.js"></script>
		<script type="text/javascript">
			// From JavaScript, you can parse with your favorite JSON library.
			var sr = JSON.parse('<?php echo $context; ?>');
			
			// Reference the Chatter user's URL from Context.Links object.
			function postChatterFeed () {
				var url = sr.context.links.chatterFeedsUrl+"/news/"+sr.context.user.userId+"/feed-items";
				var body = {body : {messageSegments : [{type: "Text", text: $("#postBox").val()}]}};

				Sfdc.canvas.client.ajax(
					url,
					{client : sr.client,
						method: 'POST',
						contentType: "application/json",
						data: JSON.stringify(body),
						success : function(data) {
							if (201 === data.status) {
								alert("Success");
							}
						}
					}
				);
			}
		</script>
	</head>
	<body>
		<input type="text" id="postBox"/>
		<button onclick="postChatterFeed(); return false;">Chatter送信</button>
	</body>
</html>