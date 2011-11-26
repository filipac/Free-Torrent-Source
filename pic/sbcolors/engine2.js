	var rezultat = "";			//not used yet
	var resultsPerPage = 5;
	var page = 1; 				//default page with results
	var RO = null;
	var linkuriPagini = ""; 	//linkuripe pentru paginare
	var slideDeschis = false;	//flag to see if the result window is opened or not
	var search_text = "";		//text to search in the db
	var table = "";				
	var	field = "";
	var RC_domain = "http://www.resursecrestine.ro/";
	var inchide = "Click aici pentru a inchide afisarea rezultatelor";
	var deschide = 'Cauta resurse pe <a style="color: black;" href="http://www.resursecrestine.ro">www.resursecrestine.ro</a> - Click aici pentru a afisa rezultatele';
	var referer = window.location;
	var ver = "1.0 RC 2";
	
	
	//generates the pages, with blind links
	function generatePages(total) {				
		paginiHTML = "";
		nrPagini = total;		
		if (total > 5) {
			nrPagini = Math.floor(total / resultsPerPage);
			if (nrPagini * resultsPerPage < total) nrPagini++;
		}
		else nrPagini = 1;
		
		if (nrPagini != 1) {
			paginiHTML = "Pagina ";
			for (i = 0; i < nrPagini - 1; i++) {
				paginiHTML += '<a class="RC_link" onclick="javascript: print_search_results(\'aaa\',' + '\'' + (i + 1) + '\')">' + (i + 1) + "</a> <b>&middot;</b> ";				
			}
			paginiHTML += '<a class="RC_link" onclick="javascript: print_search_results(\'aaa\',' + '\'' + (nrPagini) + '\')">' + (nrPagini) + "</a>";				
			paginiHTML += '<br>';
		}
		
		return paginiHTML;
	}
	
	//for a given page fetches the data from the object and prepares it for pagination
	function generatePageContent(page, resultObject) {	
		
		len = resultObject.Results.length;		
		if (page == 1) {
			start = 0;
			end = 5;
			if (len < 5) end = len;
		}
		else {
			start = (page - 1) * resultsPerPage - 1;
			end = start + resultsPerPage;
			if (end > len) end = len;			
		}
		
		if (len < 5 || end > len) end = len;
		
		//aceasta variabila va tine textul care e continut in paginatie
		content = "";
		
		for (var i = start; i < end; i++) {
			table2 = table;
			//fetch-uim tot obiectul in variabilele acestea pentru a nu avea sursa prea lunga
			RC_titlu = resultObject.Results[i].titlu;
			RC_id = resultObject.Results[i].id;
			RC_autor = resultObject.Results[i].autor;
			RC_album = resultObject.Results[i].album;
			RC_volum = resultObject.Results[i].volum;
			
			link = table + "-" + RC_titlu + '-' + RC_id + ".htm";
			
			typeof RC_autor != "undefined" ? autor = " &middot; " + RC_autor : autor = "";
			
			if (typeof RC_album != "undefined")
				album = " &middot; " + RC_album;
			else {
				if (typeof RC_volum != "undefined")
					album = " &middot; " + RC_volum;
				else album = "";			
			}
						
			if (album == " &middot; ") album = "";
			if (autor == " &middot; ") autor = "";									

			//INCONSISTENTA 1:
			//daca cautam in tabelul predici, cand generam linkul tabelul trebuie sa il schimbam in predici, pentru ca noi
			//folosim tabelul de cautare si pentru generarea linkului iar schitele, devotionalele, etc sunt in domeniul predici
			if (table == "schite" || table == "devotionale" || table == "studii") table2 = "predici";									
			
			url = '<a target="_blank" href="http://' + table2 + '.resursecrestine.ro/' + link + '">' + RC_titlu + album + autor + '</a>';
			//INCONSISTENTA 2:
			if (table == "video_resurse" || table == "predicimp3" || table == "partituri" || table == "carti" || table == "mp3") 
				url = '<a target="_blank" href="' + RC_domain +'count.php?id=' + RC_id + '&tabel=' + table + '">' + RC_titlu + album + autor + '</a>';
		
			//INCONSISTENTA 3:				
			if (table == "emisiuni_radio" || table == "marturii" || table == "poezii_audio" || table == "povestiri_biblice" || table == "carti_audio")
				url = '<a target="_blank" href="' + RC_domain +'count.php?id=' + RC_id + '&tabel=resurse_audio">' + RC_titlu + album + autor + '</a>';
			
			if (table == "maxime")
				url = '<a target="_blank" href="http://maxime.resursecrestine.ro">' + RC_titlu + album + autor + '</a>';
			if (table == "powerpoint")
				url = '<a target="_blank" href="http://cantece.resursecrestine.ro/' + link + '">' + RC_titlu + album + autor + '</a>';
			if (table == "scenete")
				url = '<a target="_blank" href="http://eseuri.resursecrestine.ro/' + link + '">' + RC_titlu + album + autor + '</a>';
			if (table == "acorduri")
				url = '<a target="_blank" href="http://acorduri.resursecrestine.ro/' + link + '">' + RC_titlu + album + autor + '</a>';
			url = url + '<br>';
			content += url;						
		}

		//daca tabelul a fost cel cu resurse video atunci o sa punem un text in plus, cu reclama la toate video-urile
		if (table == "video_resurse") content += 'Pentru toate materialele video vizitati: <a target="_blank" href="http://video.resursecrestine.ro/">http://video.resursecrestine.ro</a>';
		
		if (content == "") content = 'Nu s-a gasit nici un rezultat! <br> Pentru mai multe detalii vizitati <a target="_blank" href="' + RC_domain + '">' + RC_domain + '</a>';
		return content;
	}
	
	
	//displays the result
	function print_search_results(resultObject, pageToDisplay) {	
				
		if (resultObject == "aaa") {
			resultObject = RO;			
		}
		
		if (typeof pageToDisplay == "undefined") {
			pageToDisplay = 1;
			RO = resultObject;	
			linkuriPagini = generatePages(resultObject.Results.length);					
		}
		
		document.getElementById('dhtmlgoodies_a1').innerHTML = "";
		
		content = '<div id="dhtmlgoodies_ac1" class="dhtmlgoodies_answer_content">';
			content += linkuriPagini;			
			content += generatePageContent(pageToDisplay, resultObject);		
		content += '</div>';		
		
		document.getElementById('dhtmlgoodies_a1').innerHTML += content;
		if (!slideDeschis) {
			showHideContent(false,1); // Automatically expand first item
//			slideDeschis = true;			
		}				
	}	
	
	/**
	*	the folowing functions are for quering the server
	*/
	
	function DummyFunction() {
		
	}
	
	
	//inserts the SCRIPT tag, thus quering the server
	function do_ajax (url) {
		
		// Create new JS element
        var jsel = document.createElement('SCRIPT');
        jsel.type = 'text/javascript';
        jsel.src = url;        
		
        // Append JS element (therefore executing the 'AJAX' call)
         document.body.appendChild (jsel);         
        // document.getElementsByTagName("head")[0].appendChild (jsel);
	}
	
	//makes the request to the server
	function make_request() {
		//if the slide is opened close it; this is eye candy
		if (slideDeschis) {			
			showHideContent(false,1); // Automatically expand first item			
			setTimeout("DummyFunction()", 250);
		}
		
		search_text = document.getElementById('search_text').value;
		search_text = escape(search_text);		

		table = document.getElementById('table').value;
		field = document.getElementById('field').value;		
		parameters = 'search_string=' + search_text + '&table=' + table + '&field=' + field + '&output=json&callback=print_search_results&referer=' + referer + '&ver=' + ver;
		url = 'http://www.resursecrestine.ro/RCWS.php?' + parameters;		
			  
		do_ajax(url);
	}
	