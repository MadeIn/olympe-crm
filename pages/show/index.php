<?php include( $_SERVER['DOCUMENT_ROOT'] . "/show/inc/param.php"); 
$titre_page = "Agenda - Olympe Mariage";
$desc_page = "Agenda - Olympe Mariage";

$sql = "select * from showrooms where showroom_num='" . $u->mShowroom . "'";
$ss = $base->query($sql);
if ($rss=mysql_fetch_array($ss)) {
	$u->mShowroomInfo = $rss;
}
?>
<?php include( $chemin . "/show/mod/head.php"); ?>
    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <?php include( $chemin . "/show/mod/top.php"); ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN PAGE BASE CONTENT -->
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light portlet-fit bordered calendar">
								<div class="portlet-title">
									<div class="caption">
										<i class=" icon-layers font-green"></i>
										<span class="caption-subject font-green sbold uppercase">Agenda</span>
									</div>
								</div>
								<div class="portlet-body">
									<div class="row">
										<div class="col-md-12 col-sm-12">
											<div id="calendar" class="has-toolbar"> </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END PAGE BASE CONTENT -->
                </div>
				<?
					$date_deb = date("Y-m-d 00:00:00", strtotime("-60 days"));
					$param = "";
					 
					// ON recherche les events pour remplir le calendrier du showroom
					$date_deb = date("Y-m-d 00:00:00", strtotime("-60 days"));
					$sql = "select * from calendriers c, calendriers_themes ct, rendez_vous r where c.rdv_num=r.rdv_num and c.theme_num=ct.theme_num and showroom_num='" . $u->mShowroom . "' and c.theme_num=1 and r.type_num IN (4,9,5) and calendrier_datedeb>'" . $date_deb . "' order by calendrier_datedeb DESC";
					$cc = $base->query($sql);
					$nbr = count($cc);
					$i=0;
					foreach ($cc as $rcc) {
						if ($i>0) {
							$param .= ',';
						}
						
						list(
							$annee_deb,
							$mois_deb,
							$jour_deb,
							$heure_deb,
							$minute_deb,
							$seconde_deb ) = split('[: -]',$rcc["calendrier_datedeb"],6);
						
						list(
							$annee_fin,
							$mois_fin,
							$jour_fin,
							$heure_fin,
							$minute_fin,
							$seconde_fin ) = split('[: -]',$rcc["calendrier_datefin"],6);
							
						$mois_deb = $mois_deb-1;
						$mois_fin = $mois_fin-1;
						
						$couleur = $rcc["theme_couleur"];
						if ($rcc["client_num"]!=0) {
							$sql = "select * from rendez_vous r, rdv_types t where r.type_num=t.type_num and rdv_num='" . $rcc["rdv_num"] . "'";
							$rr = $base->query($sql);
							if ($rrr=mysql_fetch_array($rr)) {
								$couleur = $rrr["type_couleur"];								
							}
						}
						$link = "";
						if ($rcc["client_num"]!=0) {
							$sql = "select * from clients where client_num='" . $rcc["client_num"] . "'";
							$cl = $base->query($sql);
							if ($rcl=mysql_fetch_array($cl)) {
								$link = '/clients/client.php?client_num=' . crypte($rcc["client_num"]);
							}
						}
						
						$param .= '{
								title: "' . $rcc["calendrier_titre"] . '",
								start: new Date(' . $annee_deb . ', ' . $mois_deb . ', ' . $jour_deb . ' , ' . $heure_deb . ', ' . $minute_deb . '),
								end: new Date(' . $annee_fin . ', ' . $mois_fin . ', ' . $jour_fin . ' , ' . $heure_fin . ', ' . $minute_fin . '),
								backgroundColor: App.getBrandColor("' . $couleur . '"),';
						if ($link!="")
							$param .= ' url:"' . $link . '",';
						$param .= '	allDay: !1
							 }';
						$i++;
					 }
?>					
				<?php $link_script = '<script language="JavaScript">
		var AppCalendar = function() {
			return {
				init: function() {
					this.initCalendar()
				},
				initCalendar: function() {
					if (jQuery().fullCalendar) {
						var e = new Date,
							t = e.getDate(),
							a = e.getMonth(),
							n = e.getFullYear(),
							r = {};
						App.isRTL() ? $("#calendar").parents(".portlet").width() <= 720 ? ($("#calendar").addClass("mobile"), r = {
							right: "title, prev, next",
							center: "",
							left: "agendaDay, agendaWeek, month, today"
						}) : ($("#calendar").removeClass("mobile"), r = {
							right: "title",
							center: "",
							left: "agendaDay, agendaWeek, month, today, prev,next"
						}) : $("#calendar").parents(".portlet").width() <= 720 ? ($("#calendar").addClass("mobile"), r = {
							left: "title, prev, next",
							center: "",
							right: "today,month,agendaWeek,agendaDay"
						}) : ($("#calendar").removeClass("mobile"), r = {
							left: "title",
							center: "",
							right: "prev,next,today,month,agendaWeek,agendaDay"
						});
						var l = function(e) {
								var t = {
									title: $.trim(e.text())
								};
								e.data("eventObject", t), e.draggable({
									zIndex: 999,
									revert: !0,
									revertDuration: 0
								})
							},
							o = function(e) {
								e = 0 === e.length ? "Untitled Event" : e;
								var t = $(\'<div class="external-event label label-default">\' + e + "</div>");
								jQuery("#event_box").append(t), l(t)
							};
						$("#external-events div.external-event").each(function() {
							l($(this))
						}), $("#event_add").unbind("click").click(function() {
							var e = $("#event_title").val();
							o(e)
						}), $("#event_box").html(""),  $("#calendar").fullCalendar("destroy"), $("#calendar").fullCalendar({
							header: r,
							defaultView: "agendaWeek",
							slotMinutes: 15,
							editable: 0,
							droppable: 0,
							drop: function(e, t) {
								var a = $(this).data("eventObject"),
									n = $.extend({}, a);
								n.start = e, n.allDay = t, n.className = $(this).attr("data-class"), $("#calendar").fullCalendar("renderEvent", n, !0), $("#drop-remove").is(":checked") && $(this).remove()
							},
							events: [' . $param . ']
						})
					}
				}
			}
		}();
		jQuery(document).ready(function() {
			AppCalendar.init()
		});
		
		</script>';
		$link_script .= '<script src="/assets/global/plugins/fullcalendar/lang/fr.js" type="text/javascript"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">';
		?>
                <?php include( $chemin . "/show/mod/footer.php"); ?>
            </div>
        </div>
         <?php  include( $chemin . "/show/mod/bottom.php"); ?>
		
    </body>

</html>