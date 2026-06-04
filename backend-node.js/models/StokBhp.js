const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const StokBhp = sequelize.define('StokBhp', {
    jumlah_stok: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    minimal_stok: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    barang_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    }
}, {
  tableName: 'stok_bhp',
  timestamps: false 
});

module.exports = StokBhp;
