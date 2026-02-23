# TrisTras Ileapaindegia - Web App

TrisTras ileapaindegiaren kudeaketarako eta erreserbak egiteko web aplikazio modernoa da. Eskuz landutako diseinu premium bat eskaintzen du, mobile-first ikuspegiarekin eta euskarazko interfaze osoarekin.

## 🚀 Ezaugarri Nagusiak

Aplikazioak bi profil nagusi ditu: **Bezeroak** eta **Langileak (Barberuak)**.

### Bezeroen Atala
- **Erreserba Sistema**: Zerbitzuak (mozketa, bizarra, konboa), langilea, data eta ordua hautatzeko interfaze intuitiboa.
- **Ordainketa Segurua**: Simulatutako ordainketa pasarela txartel bidez.
- **Panel Pertsonala**: Hurrengo hitzorduen ikuspegia eta hitzorduen historia osoa.
- **Profila Kudeatu**: Datu pertsonalak eta ezarpenak aldatzeko aukera.

### Langileen Atala (Barber Dashboard)
- **Gaurko Agenda**: Gaurko hitzordu guztien zerrenda denbora errealean.
- **Egoera Aldaketa**: Lanean edo atsedenaldian egotea toggle bidez kudeatu.
- **Bezeroen Zerrenda**: Bezeroen datu-basea bilatzailearekin.
- **Estatistika Azkarrak**: Geratzen diren hitzorduen eta hurrengo atsedenaldiaren informazioa.

## 🎨 Diseinua eta Estetika

Aplikazioak **Glassmorphism** estetika erabiltzen du, dark theme premium batekin konbinatuta.
- **Semantic Basque CSS**: Tailwind CSS-tik trantsizioa egin dugu egitura semantikoago eta garbiago batera (`css/estiloak.css`).
- **Responsiveness**: Mahaigaineko pantailetarako egokitua, mugikorrerako esperientzia bikaina mantenduz.
- **Micro-animations**: Interakzio leunak eta feedback bisuala botoietan eta txarteletan.

## 🛠️ Teknologia Stack-a

- **Backend**: PHP 8.x
- **Database**: MySQL (PDO segurua, SQL injection prebenitzeko)
- **Frontend**: HTML5, Vanilla CSS3, JavaScript (jQuery interakzio batzuetarako)
- **Diseinu Sistema**: Ohiko CSS klase semantikoak (Basque naming convention)

## 📂 Proiektuaren Egitura

```text
├── css/            # Estilo fitxategiak (estiloak.css, orriak/)
├── js/             # Script lagungarriak
├── includes/       # PHP modularra (db connection, header, footer)
├── irudiak/        # Asset grafikoak eta hero irudiak
├── sql/            # Datubasearen setup scriptak (Basque schema)
├── sesioa/         # Saio kudeaketarako PHP fitxategiak
├── bezeroak_php/   # Bezeroen paneleko orriak
├── langileak_php/  # Langileen paneleko orriak
├── index.php       # Landing page nagusia
```

## ⚙️ Instalazioa

1. Klonatu biltegia:
```bash
git clone https://github.com/AnderUrienGoierri/TrisTras_Ileapaindegia.git
```
2. Inportatu `sql/datubasea_setup.sql` zure MySQL zerbitzarian.
3. Konfiguratu datu-basearen konexioa `includes/db.php` fitxategian.
4. Zerbitzatu proiektua XAMPP edo antzeko web zerbitzari bati esker.

---
**TrisTras Team** - Estiloa eta Nortasuna.
