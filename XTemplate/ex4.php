<?

	/* 
		example 4
		demonstrates recursive parse



	*/

	require "xtpl.p";

	$xtpl=new XTemplate ("ex4.xtpl");
	$xtpl->rparse("main");
	$xtpl->out("main");

/*

		Revision 1.1  2004/05/27 05:30:47  sugarjacob
		Moving project to SourceForge.

		Revision 1.1  2004/05/19 01:48:20  sugarcrm
		Adding files with binary option as appropriate.
		
		Revision 1.2  2001/03/26 23:25:02  cranx
		added keyword expansion to be more clear
		
*/

?>
