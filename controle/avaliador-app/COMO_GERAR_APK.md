# 📱 Como Gerar o APK do Avaliador Sabores

O projeto Capacitor já está configurado em `/avaliador-app/`. Agora você precisa gerar o APK.

## 🎯 OPÇÃO 1: Usar Serviço Online (MAIS RÁPIDO) ⭐ RECOMENDADO

### **Usando o Expo Application Services (EAS)**

1. Instale o Expo CLI:
   ```bash
   npm install -g eas-cli
   ```

2. Faça login ou crie conta:
   ```bash
   eas login
   ```

3. Configure e faça build:
   ```bash
   cd avaliador-app
   eas build --platform android --profile preview
   ```

4. Baixe o APK quando estiver pronto

### **Usando AppFlow (Ionic)**

1. Acesse: https://ionic.io/appflow
2. Crie uma conta gratuita
3. Faça upload do projeto
4. Configure Android build
5. Baixe o APK

## 🖥️ OPÇÃO 2: Gerar Localmente com Android Studio

### **Passo 1: Instalar Android Studio**

1. Baixe: https://developer.android.com/studio
2. Instale o Android Studio
3. Abra e vá em: **Tools → SDK Manager**
4. Instale:
   - Android SDK (API 33 ou superior)
   - Android SDK Build-Tools
   - Android Emulator (opcional)

### **Passo 2: Configurar Variáveis de Ambiente**

No Mac, adicione ao `~/.zshrc` ou `~/.bash_profile`:

```bash
export ANDROID_HOME=$HOME/Library/Android/sdk
export PATH=$PATH:$ANDROID_HOME/emulator
export PATH=$PATH:$ANDROID_HOME/platform-tools
export PATH=$PATH:$ANDROID_HOME/tools
export PATH=$PATH:$ANDROID_HOME/tools/bin
```

Depois execute:
```bash
source ~/.zshrc
```

### **Passo 3: Instalar Java JDK**

```bash
brew install openjdk@17
```

Adicione ao path:
```bash
export JAVA_HOME=/opt/homebrew/opt/openjdk@17
export PATH=$JAVA_HOME/bin:$PATH
```

### **Passo 4: Abrir Projeto no Android Studio**

1. Abra Android Studio
2. File → Open
3. Navegue até: `/Applications/XAMPP/xamppfiles/htdocs/controle/avaliador-app/android`
4. Clique em "Open"
5. Aguarde o Gradle Sync terminar

### **Passo 5: Gerar APK**

1. No Android Studio: **Build → Build Bundle(s) / APK(s) → Build APK(s)**
2. Aguarde o build terminar
3. Clique em "locate" quando aparecer a notificação
4. O APK estará em: `android/app/build/outputs/apk/debug/app-debug.apk`

## 📦 OPÇÃO 3: Gerar APK via Linha de Comando

Se você já tem Android Studio e Java configurados:

```bash
cd avaliador-app/android
./gradlew assembleDebug
```

O APK estará em: `app/build/outputs/apk/debug/app-debug.apk`

## 🚀 OPÇÃO 4: Usar Website2APK Builder (MAIS SIMPLES)

Se você só quer um APK simples que carrega a URL:

1. Acesse: https://website2apk.com/
2. Cole a URL: `https://saboresemmovimento.com.br/controle/avaliador`
3. Nome do app: "Avaliador Sabores"
4. Faça upload do ícone (icon-192.png)
5. Clique em "Create APK"
6. Baixe o APK gerado

**Vantagens:**
- ✅ Não precisa instalar nada
- ✅ Gera em 2 minutos
- ✅ Funciona offline depois de carregar

**Desvantagens:**
- ❌ Menos controle sobre recursos
- ❌ Pode ter anúncios (versão grátis)

## 📲 Instalar o APK no Tablet

1. Copie o APK para o tablet (via USB, email, Google Drive, etc)
2. No tablet, abra o arquivo APK
3. Se pedir, habilite "Instalar apps de fontes desconhecidas"
4. Confirme a instalação
5. Abra o app

## 🔧 Troubleshooting

### Erro: "SDK not found"
- Instale o Android SDK pelo Android Studio

### Erro: "Java not found"
- Instale Java JDK 17

### Erro: "Gradle sync failed"
- Delete a pasta `android/.gradle`
- Abra novamente no Android Studio

## 📝 Notas

- O APK gerado é de **DEBUG** (para desenvolvimento)
- Para produção, use APK assinado (release)
- O app simplesmente redireciona para a URL do sistema
- Funciona offline se o PWA do site funcionar
