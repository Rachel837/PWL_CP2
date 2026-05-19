const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const KategoriBarang = sequelize.define('KategoriBarang', {
    nama_kategori: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'kategori_barang',
  timestamps: false 
});

module.exports = KategoriBarang;
