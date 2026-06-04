const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 
const RiwayatBarang = sequelize.define('RiwayatBarang', {
    status: {
      type: DataTypes.STRING,
      allowNull: true
    },
    tanggal: {
      type: DataTypes.STRING,
      allowNull: true
    },
    keterangan: {
      type: DataTypes.STRING,
      allowNull: true
    },
    inventaris_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    }
}, {
  tableName: 'riwayat_barang',
  timestamps: false 
});

module.exports = RiwayatBarang;
