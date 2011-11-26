function fetch_object(idname)
{
	if (document.getElementById)
	{
		return document.getElementById(idname);
	}
	else if (document.all)
	{
		return document.all[idname];
	}
	else if (document.layers)
	{
		return document.layers[idname];
	}
	else
	{
		return null;
	}
}
function construct_phrase()
{
	if (!arguments || arguments.length < 1 || !is_regexp)
	{
		return false;
	}

	var args = arguments;
	var str = args[0];
	var re;

	for (var i = 1; i < args.length; i++)
	{
		re = new RegExp("%" + i + "\\$s", 'gi');
		str = str.replace(re, args[i]);
	}
	return str;
}
function detect_caps_lock(e)
{
	e = (e ? e : window.event);

	var keycode = (e.which ? e.which : (e.keyCode ? e.keyCode : (e.charCode ? e.charCode : 0)));
	var shifted = (e.shiftKey || (e.modifiers && (e.modifiers & 4)));
	var ctrled = (e.ctrlKey || (e.modifiers && (e.modifiers & 2)));

	// if characters are uppercase without shift, or lowercase with shift, caps-lock is on.
	return (keycode >= 65 && keycode <= 90 && !shifted && !ctrled) || (keycode >= 97 && keycode <= 122 && shifted);
}