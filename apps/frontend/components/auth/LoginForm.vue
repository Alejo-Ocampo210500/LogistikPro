<template>
  <v-card light elevation="0" color="#ffffff" class="pa-6 pa-sm-8 pa-lg-10 login-card">
    <div class="card-index mb-5">01 / IDENTIFICACIÓN</div>

    <div class="text-h4 font-weight-black mb-2 login-title">
      Iniciar sesión
    </div>

    <div class="body-2 mb-8 login-subtitle">
      Ingresa con tu cuenta para continuar en LogistikPro.
    </div>

    <v-alert v-if="errorMessage" dense outlined type="error" class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <v-form ref="form" autocomplete="off" @submit.prevent="handleSubmit">
      <label class="field-label" for="auth-email">Correo electrónico</label>
      <v-text-field id="auth-email" v-model.trim="form.email" name="auth_email" placeholder="nombre@empresa.com" type="email" light outlined
        autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" required color="#143b7a"
        :disabled="isSubmitting" :rules="emailRules" prepend-inner-icon="mdi-email-outline" class="mb-2 auth-field" />

      <label class="field-label" for="auth-password">Contraseña</label>
      <v-text-field id="auth-password" v-model="form.password" name="auth_password" :type="showPassword ? 'text' : 'password'"
        placeholder="Ingresa tu contraseña" light outlined autocomplete="new-password" required color="#143b7a" :disabled="isSubmitting"
        :rules="passwordRules" prepend-inner-icon="mdi-lock-outline" class="mb-2 auth-field"
        :append-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        @click:append="showPassword = !showPassword" />

      <v-checkbox v-model="rememberSession" label="Mantener sesión iniciada" light :disabled="isSubmitting"
        color="#143b7a" hide-details class="mt-n1 remember-check" />

      <v-btn block x-large color="#0b2550" class="mt-7 font-weight-bold login-btn" :loading="isSubmitting"
        :disabled="isSubmitting" type="submit">
        Entrar al sistema
        <v-icon right size="19">mdi-arrow-right</v-icon>
      </v-btn>
    </v-form>
  </v-card>
</template>

<script>
export default {
  name: 'LoginForm',

  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      rememberSession: false,
      showPassword: false,
      isSubmitting: false,
      errorMessage: '',
      emailRules: [
        (v) => !!v || 'El correo es obligatorio.',
        (v) => /.+@.+\..+/.test(v) || 'Ingresa un correo válido.',
      ],
      passwordRules: [
        (v) => !!v || 'La contraseña es obligatoria.',
      ],
    };
  },

  mounted() {
    // Fuerza estado inicial limpio aunque el navegador intente autocompletar.
    this.form.email = '';
    this.form.password = '';
    this.$nextTick(() => {
      this.$refs.form?.resetValidation?.();
    });
  },

  methods: {
    async handleSubmit() {
      this.errorMessage = '';

      const formIsValid = this.$refs.form?.validate?.();
      if (!formIsValid) {
        return;
      }

      this.isSubmitting = true;
      this.$emit('start-action', 'Validando credenciales...');

      try {
        const { data } = await this.$api.post('/auth/login', {
          email: this.form.email,
          password: this.form.password,
        });

        const token = data?.access_token || data?.token || data?.data?.access_token || data?.data?.token;
        if (token) {
          this.$setApiToken(token);
        }

        this.$emit('authenticated', data);
      } catch (error) {
        this.errorMessage =
          error?.response?.data?.mensaje ||
          error?.response?.data?.message ||
          'No fue posible iniciar sesión. Revisa tus datos e inténtalo nuevamente.';
      } finally {
        this.isSubmitting = false;
        this.$emit('stop-action');
      }
    },
  },
};

</script>

<style scoped>
.login-card {
  position: relative;
  overflow: hidden;
  border: 1px solid #d8e0eb;
  border-radius: 24px !important;
  box-shadow: 0 24px 60px rgba(7, 22, 45, 0.14) !important;
}

.login-card::before { content: ''; position: absolute; top: 0; left: 0; width: 92px; height: 5px; background: #f4b640; }
.card-index { color: #8292aa; font-size: 10px; font-weight: 800; letter-spacing: 0.16em; }
.login-title { color: #07162d; letter-spacing: -0.035em; }
.login-subtitle { color: #65758f; line-height: 1.6; }
.field-label { display: block; margin-bottom: 8px; color: #253957; font-size: 11px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }

.auth-field ::v-deep .v-input__slot {
  min-height: 58px !important;
  border-radius: 14px !important;
  background: #f7f9fc !important;
}

.auth-field ::v-deep fieldset { border-color: #ccd6e4 !important; }
.auth-field ::v-deep .v-input__slot:hover fieldset { border-color: #8192ab !important; }
.auth-field ::v-deep .v-input__prepend-inner { margin-right: 8px; }
.auth-field ::v-deep .v-icon { color: #65758f; }
.remember-check ::v-deep .v-label { color: #52647d; font-size: 13px; }
.login-btn { height: 58px !important; border-radius: 14px !important; color: #fff !important; letter-spacing: 0.06em; box-shadow: 0 12px 24px rgba(11, 37, 80, 0.22); transition: transform 0.2s ease, box-shadow 0.2s ease; }
.login-btn:hover { transform: translateY(-2px); box-shadow: 0 16px 28px rgba(11, 37, 80, 0.28); }

@media (max-width: 600px) {
  .login-card {
    padding: 32px 24px !important;
    border-radius: 28px !important;
  }

  .login-title { font-size: 1.8rem !important; }
  .login-btn { letter-spacing: 0.03em; }
  .auth-field { margin-bottom: 2px !important; }
}
</style>
