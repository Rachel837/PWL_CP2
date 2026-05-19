const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Ruangan = sequelize.define('Ruangan', {
    kode_ruangan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    nama_ruangan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    lokasi: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'ruangan',
  timestamps: false 
});

module.exports = Ruangan;
