// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	BASEURL + '/forums/preview.php', // path to your BBCode parser
	markupSet: [
		{name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]', className:'bold'},
		{name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]', className:'italic'},
		{name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]', className:'underline'},
		{separator:'---------------' },
		{name:'Picture', key:'P', replaceWith:'[img][![Url]!][/img]', className:'picture'},
		{name:'Link', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...', className:'link'},
		{separator:'---------------' },
		{name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'7', openWith:'[size=7]', closeWith:'[/size]' },
			{name:'6', openWith:'[size=6]', closeWith:'[/size]' },
			{name:'5', openWith:'[size=5]', closeWith:'[/size]' },
			{name:'4', openWith:'[size=4]', closeWith:'[/size]' },
			{name:'3', openWith:'[size=3]', closeWith:'[/size]' },
			{name:'2', openWith:'[size=2]', closeWith:'[/size]' },
			{name:'1', openWith:'[size=1]', closeWith:'[/size]' },
		], className:'fonts'},
		{	name:'Colors', 
			className:'colors', 
			openWith:'[color=[![Color]!]]', 
			closeWith:'[/color]', 
				dropMenu: [
					{name:'Yellow',	openWith:'[color=yellow]', 	closeWith:'[/color]', className:"col1-1" },
					{name:'Orange',	openWith:'[color=orange]', 	closeWith:'[/color]', className:"col1-2" },
					{name:'Red', 	openWith:'[color=red]', 	closeWith:'[/color]', className:"col1-3" },
					
					{name:'Blue', 	openWith:'[color=blue]', 	closeWith:'[/color]', className:"col2-1" },
					{name:'Purple', openWith:'[color=purple]', 	closeWith:'[/color]', className:"col2-2" },
					{name:'Green', 	openWith:'[color=green]', 	closeWith:'[/color]', className:"col2-3" },
					
					{name:'White', 	openWith:'[color=white]', 	closeWith:'[/color]', className:"col3-1" },
					{name:'Gray', 	openWith:'[color=gray]', 	closeWith:'[/color]', className:"col3-2" },
					{name:'Black',	openWith:'[color=black]', 	closeWith:'[/color]', className:"col3-3" }
				]
		},
		{	name:'Table generator', 
		className:'tablegenerator', 
		placeholder:"Your text here...",
		replaceWith:function(h) {
			cols = prompt("How many cols?");
			rows = prompt("How many rows?");
			html = "";
			for (r = 0; r < rows; r++) {
				for (c = 0; c < cols; c++) {
					html += "|"+(h.placeholder||"");	
				}
				html += "|\n";
			}
			return html;
		}
	},
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'[list]\n', closeWith:'\n[/list]', className:'listbullet'},
		{name:'List item', openWith:'[*] ', className:'listitem'},
		{separator:'---------------' },
		{name:'Left', openWith:'[left]', closeWith:'[/left]', className:'Left'},
		{name:'Center', openWith:'[center]', closeWith:'[/center]', className:'center'},
		{name:'Right', openWith:'[right]', closeWith:'[/right]', className:'Right'},
		{separator:'---------------' },
		{name:'Quotes', openWith:'[quote]', closeWith:'[/quote]', className:'quotes'},
		{name:'Code', openWith:'[code]', closeWith:'[/code]', className:'code'}, 
		{separator:'---------------' },
{	name:'Email selection', 
		className:'email', 
		beforeInsert:function(h) { 
				if (h.altKey) {
					email = prompt("Email:");
				} else {
					email = "";	
				}
				subject = prompt("Subject:", "From markItUp! editor");
				document.location="mailto:"+email+"?subject="+escape(subject)+"&body="+escape(h.selection); 
			} 
		},
{	name:'Lorem Ipsum', className:'lorem', dropMenu: [
				{name:'Lorem ipsum...', className:'lorem-special', replaceWith:'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.' },
				{name:'Suspendisse...', className:'lorem-special', replaceWith:'Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.' },
				{name:'Maecenas...', className:'lorem-special', replaceWith:'Maecenas ligula massa, varius a, semper congue, euismod non, mi.' },
				{name:'Proin porttitor...', className:'lorem-special', replaceWith:'Proin porttitor, orci nec nonummy molestie, non fermentum diam nisl sit amet erat.' },
				{name:'Duis arcu...', className:'lorem-special', replaceWith:'Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim.' },
				{name:'Long paragraph', replaceWith:'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean ut orci vel massa suscipit pulvinar. Nulla sollicitudin. Fusce varius, ligula non tempus aliquam, nunc turpis ullamcorper nibh, in tempus sapien eros vitae ligula. Pellentesque rhoncus nunc et augue. Integer id felis. Curabitur aliquet pellentesque diam. Integer quis metus vitae elit lobortis egestas. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi vel erat non mauris convallis vehicula. Nulla et sapien. Integer tortor tellus, aliquam faucibus, convallis id, congue eu, quam. Mauris ullamcorper felis vitae erat. Proin feugiat, augue non elementum posuere, metus purus iaculis lectus, et tristique ligula justo vitae magna.' }
			]
		},
		{name:'RSS Feed Grabber', className:'rssFeedGrabber', replaceWith:function(markItUp) { return miu.rssFeedGrabber(markItUp) } },
		{	name:'Date of the Day', 
			className:"dateoftheday", 
			replaceWith:function(h) { 
				date = new Date()
				weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
				monthname = ["January","February","March","April","May","June","July","August","September","October","November","December"];
				D = weekday[date.getDay()];
				d = date.getDate();
				m = monthname[date.getMonth()];
				y = date.getFullYear();
				h = date.getHours();
				i = date.getMinutes();
				s = date.getSeconds();
				return (D +" "+ d + " " + m + " " + y + " " + h + ":" + i + ":" + s);
			}
		},
		{name:'TinyUrl', className:'tinyUrl', openWith:function(markItUp) { return miu.tinyUrl(markItUp) }, closeWith:'[/url]', placeHolder:'text to link with a long url...' },
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Preview', className:"preview", call:'preview' }
	]
}
miu = {
    rssFeedGrabber: function(markItUp) {
        var feed, limit = 100;
        url = prompt('Rss Feed Url', 'http://rss.news.yahoo.com/rss/topstories');
        if (markItUp.altKey) {
            limit = prompt('Top stories', '5');
        }
        $.ajax({
                async:     false,
                type:     "POST",
                url:     markItUp.root+"utils/rssfeed/grab.php",
                data:    "url="+url+"&limit="+limit,
                success:function(content) {
                    feed = content;
                }
            }
        );    
        if (feed == "MIU:ERROR") {
            alert("Can't find a valid RSS Feed at "+url);
            return false;
        }
        return feed;
    },
 tinyUrl: function (markItUp) {
        var url, tinyUrl;
        url = prompt("Url:", "http://");
        if (url) {
            $.ajaxSetup( { async:false } );
            $.post(markItUp.root+"utils/tinyurl/get.php", "url="+url, function(content) {
                tinyUrl = content;    
                }
            );
        } else {
            tinyUrl = "";
        }
        return '[url='+tinyUrl+']';
    }
}
