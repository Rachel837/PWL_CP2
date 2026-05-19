const { Sequelize } = require('sequelize');
require('dotenv').config();

const sequelize = new Sequelize(
  process.env.DB_NAME,
  process.env.DB_USER,
  process.env.DB_PASSWORD,
  {
    host: process.env.DB_HOST,
    dialect: 'mysql',
    port: process.env.DB_PORT || 3306,
    logging: false
  }
);

sequelize.authenticate()
  .then(() => {
    console.log('Database terhubung.');
  })
  .catch(err => {
    console.error('Tidak dapat terhubung ke database:', err);
  });

module.exports = sequelize;
