<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="{{ asset('assets') . '/' }}">
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Tableau de bord - Mot de passe oublié</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="css/app.min.css">
    <link rel="stylesheet" href="bundles/bootstrap-social/bootstrap-social.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='img/favicon.ico' />
    <style>
        body, html {
            height: 100%;
        }
        #app {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .section {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
        }
        .card {
            margin-top: -50px;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card card-primary">
              <div class="card-header text-center">
                <h4>Réinitialisation du mot de passe</h4>
              </div>
              <div class="card-body">
                @if (session('status'))
                  <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                  </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate="">
                  @csrf
                  <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    
                    @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @else
                      <div class="invalid-feedback">
                        Veuillez saisir votre email
                      </div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                      Envoyer le lien de réinitialisation
                    </button>
                  </div>
                </form>

                <div class="text-center mt-4">
                  <a href="{{ route('login') }}" class="text-small">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="js/app.min.js"></script>
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <!-- Template JS File -->
  <script src="js/scripts.js"></script>
  <!-- Custom JS File -->
  <script src="js/custom.js"></script>
</body>
</html>