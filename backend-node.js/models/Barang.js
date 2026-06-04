const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const Barang = sequelize.define('Barang', {
    nama_barang: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    spesifikasi: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    satuan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    kategori_barang_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    }
}, {
  tableName: 'barang',
  timestamps: false 
});

// Relationships
const KategoriBarang = require('./KategoriBarang');
Barang.belongsTo(KategoriBarang, { foreignKey: 'kategori_barang_id', as: 'kategori' });

module.exports = Barang;
