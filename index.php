<?php include 'includes/header.php'; ?>
<section>
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <h1 class="display-5 fw-bold mb-3">Urheberrechts-Check für Musik</h1>
        <p class="lead text-secondary">Suche nach Titel, Komponist:in, Texter:in oder Interpret:in und sieh den Schutzstatus.</p>
        <form class="row g-2" action="search.php" method="get" style="max-width:720px;">
          <div class="col-sm-9"><input name="q" type="search" class="form-control form-control-lg" placeholder="z. B. »Die Forelle«" required></div>
          <div class="col-sm-3 d-grid"><button class="btn btn-primary btn-lg">Suchen</button></div>
        </form>
        <p class="small text-muted mt-2">Faustregel: EU-weit 70 Jahre post mortem auctoris. Details folgen auf der Recht-Seite. </p>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
