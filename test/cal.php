<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/css/webix.css?v=8.3.2" type="text/css"   charset="utf-8">
		<script src="/js/webix.js?v=8.3.2" type="text/javascript" charset="utf-8"></script>
		<title>Timepicker in Calendar</title>
	</head>
	<style type="text/css">
		#listA, #listB, #listC{
			float:left; margin:20px;
		}
		body{
			background: #faf6ed;
		}
		.webix_view{
			border-radius:3px;
		}
	</style>
	<body>
		<div id="listA"></div>
		

		<script type="text/javascript" charset="utf-8">
			webix.ui({
				container:"listA",
				date:new Date(2012,6,2),
				view:"calendar",
				events:webix.Date.isHoliday,
				timepicker:true
			});

			webix.ui({
				container:"listB",
				date:new Date(2012,3,16, 8, 10),
				view:"calendar",
				events:webix.Date.isHoliday,
				minuteStep: 10,
				timepicker:true
			});

			webix.ui({
				container:"listC",
				date:new Date(2012,3,16, 8, 35),
				view:"calendar",
				type:"time"
			});



		</script>
	<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');  ga('create', 'UA-41866635-1', 'auto'); ga('send', 'pageview');</script>
<script src="https://cdn.ravenjs.com/3.19.1/raven.min.js" crossorigin="anonymous"></script>
<script>Raven.config('https://417d33c31f07425cad14617d060cc3e8@sentry.webix.io/10',{ release:'8.3.2'}).install();</script>
</body>
</html>