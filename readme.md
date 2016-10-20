Προβολή Μισθοδοσίας εκπ/κών ΕΣΠΑ
-----------------------
Πρόγραμμα ανάλυσης και προβολής αρχείων μισθοδοσίας εκπ/κών ΕΣΠΑ.

1. Πιστοποιεί το χρήστη με επώνυμο και όνομα
2. Σαρώνει το φάκελο 'csv' για αρχεία τύπου csv. (από το σύστημα μισθοδοσίας [Bglossa](http://bglossa.ypepth.gr))
3. Αναλύει το κάθε αρχείο και βρίσκει τον εκπ/κό που ζητήθηκε.
4. Προβάλλει τα αποτελέσματα σε ένα όμορφο πίνακα.
5. Εξάγει σε PDF.

από τον [Βαγγέλη Ζαχαριουδάκη](http://github.com/sugarv)

Οδηγίες:

- Μεταβολή παραμέτρων στο αρχείο `config.php`
- Εγκατάσταση απαιτούμενων βιβλιοθηκών με την εντολή `composer update` σε υπολογιστή (ή το server) με εγκατεστημένο το [composer](https://getcomposer.org/)
- Ανέβασμα σε server (αν δεν έχει γίνει ήδη στο 1.)
- Ανέβασμα αρχείων csv στο φάκελο 'csv'. Αν τα αρχεία είναι της μορφής YYMM.csv (π.χ. 1610.csv), το πρόγραμμα εμφανίζει το πιο πρόσφατο αρχείο στην αρχική σελίδα.
- Το σύστημα είναι έτοιμο!


CSV parser and displayer for ESPA files
-----------------------
ESPA payment CSV parser and displayer.

1. Authenticates user with surname & afm.
2. Scans a folder for csv files (from [Bglossa](http://bglossa.ypepth.gr)) ('csv' folder)
3. Parses each file @ 'csv' folder (named YYMM.csv), finding the requested user data
4. Displays results on a user friendly table
5. Exports to PDF

by [Vangelis Zacharioudakis](http://github.com/sugarv)
