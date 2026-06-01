const sequelize = require('./config/database'); 
sequelize.query("ALTER TABLE draft_pengadaan MODIFY status enum('draft', 'submitted', 'reviewed', 'approved', 'rejected', 'finalized', 'locked') DEFAULT NULL;")
.then(res => { 
    console.log('Sukses alter tabel'); 
    process.exit(0); 
})
.catch(err => { 
    console.error(err); 
    process.exit(1); 
});
