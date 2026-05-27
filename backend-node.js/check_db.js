const { Sequelize } = require('sequelize');
const sequelize = new Sequelize('db_inventaris', 'root', '', {
    host: '127.0.0.1',
    dialect: 'mysql',
    logging: false
});

async function run() {
    try {
        const [results, metadata] = await sequelize.query("SHOW TABLES");
        console.log("TABLES:", results.map(r => Object.values(r)[0]));
        
        for (let r of results) {
            let table = Object.values(r)[0];
            const [cols] = await sequelize.query(`SHOW COLUMNS FROM ${table}`);
            console.log(`\nTable ${table} columns:`);
            console.log(cols.map(c => `${c.Field} (${c.Type})`).join(', '));
        }
    } catch (e) {
        console.error(e);
    } finally {
        sequelize.close();
    }
}
run();
