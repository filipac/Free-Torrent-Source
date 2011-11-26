<?php
	print("</td></tr></table>\n");
	print("<table align=\"center\" class=\"footer\" width=\"960px\" cellspacing=\"0\" cellpadding=\"0\"><tr valign=\"top\">\n");
	print("<td width=\"49%\" class=\"bottom\"><div align=\"center\"><br><b><br>");
	copyright();
	echo _br;
	echo ffactory::executedinandqueries()._br.ffactory::license();
	echo "</b></div><br></td>";
	print("</tr></table>\n");
	print("</body></html>\n");  #The next step is required to show the correct number of queries every refresh.
  ffactory::destroy_q();
?>