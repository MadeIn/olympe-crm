 <!-- BEGIN FOOTER -->
<p class="copyright"> <? echo Date("Y") ?> &copy; <a target="_blank" href="http://www.madein.net">Made In SARL</a> &nbsp;|&nbsp;<a href="http://www.olympe-mariage.com" title="" target="_blank">Olympe Mariage</a>
</p>
<a href="#index" class="go2top">
	<i class="icon-arrow-up"></i>
</a>
<div class="modal fade" id="idle-timeout-dialog" data-backdrop="static">
	<div class="modal-dialog modal-small">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Votre Session est expiré.</h4>
			</div>
			<div class="modal-body">
				<p>
					<i class="fa fa-warning font-red"></i> Vous allez être redirigé vers la page de connexion dans 
					<span id="idle-timeout-counter"></span> secondes.</p>
			</div>
			<div class="modal-footer">
				<button id="idle-timeout-dialog-logout" type="button" class="btn dark btn-outline sbold uppercase">Me reconnecter</button>
			</div>
		</div>
	</div>
</div>
<!-- END FOOTER -->