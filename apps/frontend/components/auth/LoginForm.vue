<template>
  <v-card light rounded="xl" elevation="12" color="#ffffff" class="pa-6 pa-md-8 login-card">
    <div class="top-accent mb-4"></div>

    <div class="d-flex align-center mb-3">
      <v-chip small outlined color="#b2c3dd" text-color="#4b5f80" class="font-weight-medium">
        Acceso seguro
      </v-chip>
    </div>

    <div class="text-h5 font-weight-black mb-1 secondary--text text--darken-2">
      Iniciar sesión
    </div>

    <div class="body-2 mb-6 blue-grey--text text--darken-1">
      Ingresa con tu cuenta para continuar en LogistikPro.
    </div>

    <v-alert v-if="errorMessage" dense outlined type="error" class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <v-form ref="form" autocomplete="off" @submit.prevent="handleSubmit">
      <v-text-field v-model.trim="form.email" name="auth_email" label="Correo electrónico" type="email" light filled
        autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" required color="secondary"
        :disabled="isSubmitting" :rules="emailRules" prepend-inner-icon="mdi-email-outline" class="mb-2 auth-field" />

      <v-text-field v-model="form.password" name="auth_password" :type="showPassword ? 'text' : 'password'"
        label="Contraseña" light filled autocomplete="new-password" required color="secondary" :disabled="isSubmitting"
        :rules="passwordRules" prepend-inner-icon="mdi-lock-outline" class="mb-2 auth-field"
        :append-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        @click:append="showPassword = !showPassword" />

      <v-checkbox v-model="rememberSession" label="Mantener sesión iniciada" light :disabled="isSubmitting"
        color="secondary" hide-details class="mt-n1" />

      <v-btn block large color="#f4b640" class="mt-5 font-weight-bold login-btn" :loading="isSubmitting"
        :disabled="isSubmitting" type="submit">
        Entrar
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
  border: 1px solid #d7e1f1;
  box-shadow: 0 18px 40px rgba(21, 53, 102, 0.12) !important;
}

.top-accent {
  height: 4px;
  border-radius: 999px;
  background: linear-gradient(90deg, #f4b640 0%, #ffe1a0 46%, #f4b640 100%);
}

.auth-field ::v-deep .v-input__slot {
  border-radius: 12px !important;
  border: 1px solid #d5deeb !important;
  background: #f5f8fd !important;
}

.login-btn {
  color: #1a2b4a;
  letter-spacing: 0.08em;
  box-shadow: 0 10px 22px rgba(244, 182, 64, 0.26);
}

@media (max-width: 600px) {
  .login-card {
    border-radius: 18px !important;
    padding: 20px 16px !important;
  }

  .login-btn {
    letter-spacing: 0.03em;
  }

  .auth-field {
    margin-bottom: 4px !important;
  }
}
</style>
